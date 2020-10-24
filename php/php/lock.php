<?php

require '../redis/redisClient.php';

/**
 * 分布式锁  下面是分布式满足条件
 * 1.互斥性 在任意时刻 只能有一个客户端持有锁
 * 2.不会发生死锁 即使有一个客户端在持有锁的期间程序发生崩溃而没有主动解锁,也能保证后续其他客户端能加锁
 * 3.解铃还须系铃人，加锁和解锁必须是同一个客户端，客户端自己不能把别人加的锁给解锁，即不能误解锁
 * Class Lock
 */
class Lock
{
    public $redis;
    public $token;
    public $lock_pool;
    public function __construct()
    {
        $this->redis = new redisClient();
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
     * $waitLock
     * @return bool
     */
    public function lock($key,$maxTtl=10,$minTtl = 2,$reTryNum = 10,$usleep=100000)
    {
        //互斥性 在任意时刻 只能有一个客户端持有锁
        $getLock = false;
        if($this->redis->exists($key)) return $getLock;
        while ($reTryNum-- >0){
            //锁的唯一标识 防止当前客户端解锁了别的客户端加的锁 对于同一个资源加锁 $key是一样的 值要不一样
            $this->token = uniqid().mt_rand(111111111,999999999);
            //同时产生父进程与子进程  父进程先于子进程执行
            $pid = pcntl_fork();
            if($pid == -1 ){
                //进程产生失败
               break;
            }elseif ($pid){
                //父进程执行逻辑 加锁并保存锁的唯一标识 NX 保证锁的互斥性，EX:保证不会发生死锁 即使程序发生意外 没有主动解锁 也能通过自动过期而解锁
                $res = $this->redis->set($key,$this->token,['NX','EX' =>$maxTtl]);
                if($res){
                    $this->lock_pool[$key] = $this->token;
                    $getLock = true;
                    break;
                }
            }else{
                // 子进程 如果某业务处理耗时过长, 发生阻塞 , 如果阻塞时间超过了锁设置的过期时间 ,那锁就会自动释放
                // 此时业务还未执行完成 就导致其他客户端获得锁,获得相同的资源 为避免这种情况的发生，就要给锁续命 直到该业务执行完毕后主动释放锁 这就又保证锁的互斥性
                $flag = true;
                do{
                    $delay = $this->redis->delayExpire($key,$this->token,$minTtl,$maxTtl);
                    if(!$delay){
                        $flag = false;
                    }
                }while($flag);
            }
            pcntl_wait();
            usleep($usleep);
        }
        return $getLock;
    }

    public function locker($key,$maxTtl=10,$minTtl = 2,$reTryNum = 10,$usleep = 10000)
    {
        //互斥性 在任意时刻 只能有一个客户端持有锁
        $getLock = false;
        if($this->redis->exists($key)) return $getLock;
        //锁的唯一标识 防止当前客户端解锁了别的客户端加的锁 对于同一个资源加锁 $key是一样的 值要不一样
        $this->token = uniqid().mt_rand(111111111,999999999);
        while($reTryNum-- >0){
            // 加锁并保存锁的唯一标识 NX 保证锁的互斥性，EX:保证不会发生死锁 即使程序发生意外 没有主动解锁 也能通过自动过期而解锁
            $res = $this->redis->set($key,$this->token,['NX','EX' =>$maxTtl]);
            if($res){
                $this->lock_pool[$key] = $this->token;
                $getLock = true;
                break;
            }
            usleep($usleep);
        }
        //同时产生父进程与子进程  父进程先于子进程执行
        $pid = pcntl_fork();
        if($pid == -1 ){
            //进程产生失败
            $this->unlock($key, $this->token);
            return false;
        }elseif ($pid){
            //$pid>0 父进程
            return $getLock;
        }else{
            // $pid=0 子进程 如果某业务处理耗时过长, 发生阻塞 , 如果阻塞时间超过了锁设置的过期时间 ,那锁就会自动释放
            // 此时业务还未执行完成 就导致其他客户端获得锁,获得相同的资源 为避免这种情况的发生，就要给锁续命 直到该业务执行完毕后主动释放锁 这就又保证锁的互斥性
            //1.等待父进程执行完毕
            sleep($reTryNum*$usleep);
            $startMonitor = microtime(true);
            $flag = true;
            do{
                $delay = $this->redis->delayExpire($key,$this->token,$minTtl,$maxTtl);
                if(!$delay){
                    $flag = false;
                }
            }while($flag);
        }
        pcntl_waitpid($pid);
    }

    public function lock_org($key,$maxTtl=10,$minTtl = 2,$reTryNum = 10,$sleep=1)
    {
        //互斥性 在任意时刻 只能有一个客户端持有锁
        if($this->redis->exists($key)) return false;
        $this->token = uniqid().mt_rand(111111111,999999999);
        $pid = pcntl_fork();
        if($pid == -1 ){
            $this->unlock($key, $token);
            return false;
        }elseif ($pid){
            $res = $this->redis->set($key,$this->token,['NX','EX' =>$maxTtl]);
            if($res){
                $this->lock_pool[$key] = $this->token;
                return true;
            }
            return false;
        }else{
            $flag = true;
            do{
                $delay = $this->redis->delayExpire($key,$this->token,$minTtl,$maxTtl);
                if(!$delay){
                    $flag = false;
                }
            }while($flag);
        }
    }

    /**
     * 延长锁的过期时间 给锁续命或续租
     * @param $key     锁名
     *
     * @param $token   锁的唯一标识 防止当前客户端解锁了别的客户端加的锁
     *
     * @param $minTtl  锁的过期时间还剩余的最大值 即所设置的锁过期时间
     *
     * @param $maxTtl  锁的过期时间还剩余的最小限制值 如果某业务处理耗时过长, 发生阻塞 , 如果阻塞时间超过了锁设置的过期时间 ,那锁就会自动释放
     *                 此时业务还未执行完成 就导致其他客户端获得锁,获得相同的资源, 执行了操作, 这就不满足高并发下锁的互斥性
     *                 所以当某业务处理耗时过长 发生阻塞时 就要检查锁的过期时间还剩余的时长是否小于此值（锁的过期时间还剩余的最小限制值）
     *                 如果小于此值 就给锁续命 知道改业务执行完毕后主动释放锁
     * @return mixed
     */
    private  function delayExpire($key,$token,$minTtl,$maxTtl){
        $script = '
            if redis.call("exists", KEYS[1]) and redis.call("GET", KEYS[1]) == ARGV[1] then
                if redis.call("ttl", KEYS[1]) <= ARGV[2] then
                    return redis.call("EXPIRE", KEYS[1],ARGV[3])
                else
                    return 1
                end
            else
                return 0
            end           
        ';
        return $this->redis->eval($script, [$key, $token,$minTtl,$maxTtl], 1);
    }

    /**
     * 释放锁
     * @param $key    锁名
     *
     * @param $token  锁的唯一标识 防止当前客户端解锁了别的客户端加的锁
     *
     * @return mixed
     */
    public function unlock($key, $token)
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        return $this->redis->eval($script, [$key, $token], 1);
    }

}



function secKill(){
    $lock = new Lock();
    $key  = "secKill";
    //获取锁
    if($lock->lock($key,10,2)){
        //假设某业务处理耗时过长,发生阻塞10s
        sleep(10);
        //业务执行完毕释放锁
        $lock->unlock($key);
    }


}