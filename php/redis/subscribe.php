<?php
//订阅
ini_set('default_socket_timeout', -1);//永不超时
require_once "redisClient.php";
$redis = new redisClient();
$channel = ['msg'];
//订阅是阻塞模式 有消息就处理; 没消息就阻塞等待  不用while(true){}结构
$redis->redis->subscribe($channel,function($redis, $chan, $msg) {
    switch($chan) {
        case 'msg':
            file_put_contents('./100.txt',$msg.PHP_EOL.PHP_EOL,FILE_APPEND);
            break;
        case 'warn':
            file_put_contents('./100.txt',$msg.PHP_EOL.PHP_EOL,FILE_APPEND);
            break;
        case 'error':
            file_put_contents('./100.txt',$msg.PHP_EOL.PHP_EOL,FILE_APPEND);
            break;
        default:
            file_put_contents('./100.txt',"default".PHP_EOL.PHP_EOL,FILE_APPEND);
    }
});


