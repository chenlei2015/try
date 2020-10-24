<?php
/**
 * Created by PhpStorm.
 * User: mandelay
 * Date: 12/03/19
 * Time: 下午 11:15
 */
require_once "redisClient.php";

$redis = new redisClient();
//例子（1）
$redis->redis->set('set mian','pp');
var_dump($redis->redis->getLastError());
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




