<?php

//require APPPATH.'/php/redis/redisClient.php';

/**
 * 悲观锁
 * 命令行  php要支持 redis、pcntl、posix、swoole 扩展
 * 锁的说明  前提单个redis服务器  能够完美的运用实际生产中
 * 分布式锁  下面是分布式满足条件
 * 1.互斥性 在任意时刻 只能有一个客户端持有锁
 * 2.不会发生死锁 即使有一个客户端在持有锁的期间程序发生崩溃而没有主动解锁,也能保证后续其他客户端能加锁
 * 3.解铃还须系铃人，加锁和解锁必须是同一个客户端，客户端自己不能把别人加的锁给解锁，即不能误解锁
 * Class Lock
 */
class Lock
{
    public $redis;
    public $token; // 锁的唯一标识 防止当前客户端解锁了别的客户端加的锁
    public $lock_pool;
    public $ParentPid;
    public function __construct()
    {
        $this->ParentPid = posix_getpid();
        file_put_contents(APPPATH.'/lock.txt',"112 start fpid:".$this->ParentPid.PHP_EOL.PHP_EOL,FILE_APPEND);
        $this->redis = new Redis();
        $this->redis->connect('192.168.71.141',7001);
        $this->redis->auth('yis@2019._');
        $this->redis->select(8);
    }

    /**
     * 获取锁
     * @param $key 锁名
     *
     * @param int $maxTtl 锁的过期时间还剩余的最大值 即所设置的锁过期时间
     *
     * @param int $minTtl 锁的过期时间还剩余的最小限制值 如果某业务处理耗时过长, 发生阻塞 , 如果阻塞时间超过了锁设置的过期时间 ,那锁就会自动释放
     *                    此时业务还未执行完成 就导致其他客户端获得锁,获得相同的资源, 执行了操作, 这就不满足高并发下锁的互斥性
     *                    所以当某业务处理耗时过长 发生阻塞时 就要检查锁的过期时间还剩余的时长是否小于此值（锁的过期时间还剩余的最小限制值）
     *                    如果小于此值,就给锁续命,直到该业务执行完毕后主动释放锁
     * @param int $reTryNum   旋时尝试获取锁的次数
     * @param int $usleep    旋时尝时间间隔
     * @return bool
     */
    public function lock($key,$maxTtl = 10,$minTtl = 2,$reTryNum = 10,$usleep = 10000)
    {
        //互斥性 在任意时刻 只能有一个客户端持有锁
        $getLock = false;
        //锁的唯一标识 防止当前客户端解锁了别的客户端加的锁 对于同一个资源加锁 $key是一样的 值要不一样
        $this->token = $this->generateUniqid();
        while($reTryNum-- >0){
            // 加锁并保存锁的唯一标识 NX 保证锁的互斥性，EX:保证不会发生死锁 即使程序发生意外 没有主动解锁 也能通过自动过期而解锁
            $res = $this->redis->set($key,$this->token,['NX','EX' =>$maxTtl]);
            if($res){
                $this->lock_pool[$key] = $this->token;
                $getLock = true;
                file_put_contents(APPPATH.'/lock.txt',"22 get lock: ".$this->ParentPid.PHP_EOL.PHP_EOL,FILE_APPEND);
                break;
            }else{
                file_put_contents(APPPATH.'/lock_exist.txt',"22 lock_exist: ".$this->ParentPid.PHP_EOL.PHP_EOL,FILE_APPEND);
            }
            usleep($usleep);
        }
        if($getLock){
            // 创建一个子进程 子进程给锁续命
            // 如果某业务处理耗时过长, 发生阻塞 , 如果阻塞时间超过了锁设置的过期时间 ,那锁就会自动释放
            // 此时父进程的业务还未执行完成 就导致其他客户端获得锁,获得相同的资源 为避免这种情况的发生，子进程就要给锁续命 直到该父进程的业务执行完毕后主动释放锁 这就又保证锁的互斥性
            // 这里会又有一个问题产生 如果父进程业务执行完毕后 发生异常 意外退出 锁没有得到释放  子进程中锁又被一直续命 就会产生死锁 这时候子进程会检测执行业务的父进程是否存在 如果不存在子进程退出 不再给锁续命
            $parent_process = new Swoole\Process(function($son_process)use($key,$minTtl,$maxTtl,$usleep){
                file_put_contents(APPPATH.'/lock.txt',"33 son process: ".microtime().PHP_EOL.PHP_EOL,FILE_APPEND);
                file_put_contents(APPPATH.'/lock.txt'," son process data: token:{$this->token}".PHP_EOL.PHP_EOL,FILE_APPEND);
                $flag = true;
                do{
                    $delay = $this->delayExpire($key,$this->token,$minTtl,$maxTtl);
                    file_put_contents(APPPATH.'/lock.txt'," 66 son process delay: ".$delay.' time: '.microtime().PHP_EOL.PHP_EOL,FILE_APPEND);
                    if(!$delay){
                        file_put_contents(APPPATH.'/lock.txt',"lock not exist : ".$delay.' time: '.microtime().PHP_EOL.PHP_EOL,FILE_APPEND);
                        $flag = false;
                    }
                    //心跳测试 查看父进程是否还在运运行
                    if(!swoole_process::kill($this->ParentPid,0)){
                        file_put_contents(APPPATH.'/lock.txt',"die parentPid:".$this->ParentPid.PHP_EOL.PHP_EOL,FILE_APPEND);
                        $flag = false;
                    }
                    usleep($usleep);
                }while($flag);
                //子进程退出
                $son_process->exit();
            });
            //子进程ID
            $sonPid = $parent_process->start();
            file_put_contents(APPPATH.'/lock.txt',"44 son process pid : ".$sonPid.' time: '.microtime().PHP_EOL.PHP_EOL,FILE_APPEND);
        }
        return $getLock;
    }

    /**
     * 生成客户端唯一标识
     */
    public function generateUniqid(){
        $microtime = explode(' ',microtime());
        $micro  = $microtime[0]*1000000;
        $second = $microtime[1];
        return $this->token = uniqid().$micro.$second.mt_rand(111111111,999999999);
    }

    /**
     * 检测父进程是否还处在
     * @param $worker
     */
    public function checkMpid(&$worker){
        //检测父进程是否还处在 $sig = 0时 检测该pid 是否存在 如果存在则返回true 否则返回false
        if(!swoole_process::kill($this->ParentPid,0)){
            $worker->exit();
            echo "这句提示,实际是看不到的.需要写到日志中,Master process exited, I [{$worker['pid']}] also quit\n";
        }
    }

    /**
     * 延长锁的过期时间 给锁续命或续租
     * @param $key     锁名
     *
     * @param $token   锁的唯一标识 防止当前客户端解锁了别的客户端加的锁
     *
     * @param $maxTtl  锁的过期时间还剩余的最大值 即所设置的锁过期时间
     *
     * @param $minTtl  锁的过期时间还剩余的最小限制值 如果某业务处理耗时过长, 发生阻塞 , 如果阻塞时间超过了锁设置的过期时间 ,那锁就会自动释放
     *                 此时业务还未执行完成 就导致其他客户端获得锁,获得相同的资源, 执行了操作, 这就不满足高并发下锁的互斥性
     *                 所以当某业务处理耗时过长 发生阻塞时 就要检查锁的过期时间还剩余的时长是否小于此值（锁的过期时间还剩余的最小限制值）
     *                 如果小于此值 就给锁续命 直到该业务执行完毕后主动释放锁
     * @return mixed
     */
    public  function delayExpire($key,$token,$minTtl,$maxTtl){
        $script = '
            if redis.call("exists", KEYS[1]) and redis.call("GET", KEYS[1]) == ARGV[1] then       
                local ttl =  redis.call("ttl", KEYS[1])         
                if tonumber(ttl) <= tonumber(ARGV[2]) then
                    return redis.call("EXPIRE", KEYS[1],ARGV[3])
                else
                    return ttl;
                end
            else
                return 0
            end
        ';
        return $this->redis->eval($script, [$key,$token,$minTtl,$maxTtl], 1);
    }


    /**
     * 释放锁
     * @param $key    锁名
     *
     * @param $token  锁的唯一标识 防止当前客户端解锁了别的客户端加的锁
     *
     * @return mixed
     */
    public function unlock($key)
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        file_put_contents(APPPATH.'/lock.txt',"77 unlock time: ".microtime().PHP_EOL.PHP_EOL,FILE_APPEND);
        return $this->redis->eval($script, [$key, $this->token], 1);
    }


}