<?php

/**
 * 令牌桶限流
 * 需要安装redis服务端的扩展redis-cell
 * Class redisClient
 */

class tokenBucket {

    protected $redis;

    protected $key = null;

    protected $max_burst = null;

    protected $tokens = null;

    protected $seconds = null;

    protected $apply = 1;

    /**
     * LeakyBucket construct
     * @param $key string
     * @param $max_burst int 初始桶数量
     * @param $tokens int 速率
     * @param $seconds int 时间
     * @param int $apply 每次漏水数量
     */
    public function __construct($key, $max_burst, $tokens, $seconds, $apply = 1)
    {
        $this->init();
        $this->key = $key;
        $this->max_burst = $max_burst;
        $this->tokens = $tokens;
        $this->seconds = $seconds;
        $this->apply = $apply;
    }

    /**
     *
     */
    public function init(){

        $this->redis = new Redis();//安装PHP的redis C语言扩展  各种操作方法名与redis命令名相同
        //$this->redis->connect('127.0.0.1',6379);
        //$this->redis->connect('192.168.71.141',7001);
        //$this->redis->auth('yis@2019._');

        $this->redis->connect('192.168.31.29',6379);
        $this->redis->auth('123456');
        $this->redis->select(0);
    }

    /**
     * 是否放行
     * @return int 0 or 1   0：放行  1:拒绝
     *  CL.THROTTLE user123 15 30 60
        0) (integer) 0    # 0 表示允许，1表示拒绝
        1) (integer) 16   # 漏斗容量
        2) (integer) 15   # 漏斗剩余空间
        3) (integer) -1   # 如果拒绝了，需要多长时间后再试(漏斗有空间了，单位秒)
        4) (integer) 2    # 多长时间后，漏斗完全空出来
     */
    public function isPass()
    {
        $rs = $this->redis->rawCommand('CL.THROTTLE', $this->key, $this->max_burst, $this->tokens, $this->seconds, $this->apply);
        return $rs;
    }


}

