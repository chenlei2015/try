<?php
/**
 * Created by PhpStorm.
 * User: mandelay
 * Date: 12/03/19
 * Time: 下午 11:15
 */

ini_set('default_socket_timeout', -1);//永不超时
require_once "redisClient.php";
require_once "tokenBucket.php";
$redis = new redisClient();


//例子（1）
//$redis->redis->set('set mian','pp');
//var_dump($redis->redis->getLastError());
//echo $redis->redis->get('mian');


//例子（2）
//$command = "redis.call('set','key_1',55);redis.call('set','key_2',66); return 'succ';" ;
//$redis->eval_command($command);


//例子（3）
//$command = "redis.call('set',KEYS[1],ARGV[1]);redis.call('set',KEYS[2],ARGV[2]); return 'succ';";
//$value = ['key_1','key_2',88,99];
//$key_num =2;
//$redis->eval_command($command,$value,$key_num);
//var_dump($redis->redis->getLastError()); //获取执行原因错误


//令牌桶
//例子（4）
$redis->redis->del('token_limit');
$bucket = new tokenBucket('token_limit',1,10,60,1);
for ($i=0;$i<60;$i++){

    //获取令牌
    $result = $bucket->isPass();
    if(!$result[0]){
        //获取到令牌 方形
        echo date("Y-m-d H:i:s").' yes'.PHP_EOL;
    }else {
        echo date("Y-m-d H:i:s").' no'.PHP_EOL;
    }


    if($result[3] >=0 ){
        //每次休眠 $result[3]+1后  可以确保每次都能拿到令牌
        sleep($result[3]+1);
    }

}








