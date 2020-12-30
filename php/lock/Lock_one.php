<?php

/**
 * 此锁的局限性： 业务的执行时间必须小于锁的过期时间  如果业务的执行时间大于锁的过期时间 锁过期后自动解锁 其他客户端可以获取锁 而当前的业务还在执行 就不满足锁的互斥性
 * 分布式锁  下面是分布式满足条件
 * 1.互斥性 在任意时刻 只能有一个客户端持有锁
 * 2.不会发生死锁 即使有一个客户端在持有锁的期间程序发生崩溃而没有主动解锁,也能保证后续其他客户端能加锁
 * 3.解铃还须系铃人，加锁和解锁必须是同一个客户端，客户端自己不能把别人加的锁给解锁，即不能误解锁
 * Class Lock
 */

class Lock_one
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
     * @param  $key 锁名
     * @param  int $maxTtl 锁的过期时间还剩余的最大值 即所设置的锁过期时间
     * @param  $reTryNum   重复获取锁的次数
     * @$usleep 时间间隔
     * @return bool
     */
    public function lock($key, $maxTtl = 10, $reTryNum = 10, $usleep = 10000)
    {
        //互斥性 在任意时刻 只能有一个客户端持有锁
        $getLock = false;
        //锁的唯一标识 防止当前客户端解锁了别的客户端加的锁 对于同一个资源加锁 $key是一样的 值要不一样
        $this->token = uniqid() . mt_rand(111111111, 999999999);
        while ($reTryNum-- > 0) {
            // 加锁并保存锁的唯一标识 NX 保证锁的互斥性，EX:保证不会发生死锁 即使程序发生意外 没有主动解锁 也能通过自动过期而解锁
            $res = $this->redis->set($key, $this->token, ['NX', 'EX' => $maxTtl]);
            if ($res) {
                $this->lock_pool[$key] = $this->token;
                $getLock = true;
                break;
            }
            usleep($usleep);
        }
        return $getLock;
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
    $lock = new Lock_one();
    $key  = "secKill";
    //获取锁
    if($lock->lock($key,5,2)){
        //假设某业务处理耗时过长,发生阻塞10s
        sleep(10);
        //业务执行完毕释放锁
        $lock->unlock($key);
    }

}
