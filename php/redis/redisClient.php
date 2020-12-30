<?php

class redisClient
{
    public $redis;
    public function __construct()
    {
        $this->redis = new Redis();//安装PHP的redis C语言扩展  各种操作方法名与redis命令名相同
        //$this->redis->connect('127.0.0.1',6379);
        $this->redis->connect('192.168.71.141',7001);
        $this->redis->auth('yis@2019._');
        $this->redis->select(0);
    }


    /**
     * 直接传入命令配置
     * @param $command  命令字符串  遵循lua语法  必选参数
     * @param $value    含有键和值的数组  可选参数
     * @param $key_num  建的数量   可选参数
     * redis.call() 可以执行redis的任何命令
     */
    public function  eval_command($command,$value,$key_num){
        $this->redis->eval($command,$value,$key_num);
    }

    public function __set($name, $value)
    {
        $this->redis->$name();
    }


    public function __destruct()
    {
        $this->redis->close();
    }

}