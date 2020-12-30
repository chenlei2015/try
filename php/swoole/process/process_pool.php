<?php
/**
 * 1、 一直执行到  $pool->start() 这里 就会有10个进程产生
 * 2、只要这段程序在运行 就会一直保持进程池 有10个进程   其中有进程执行完退出 还会有新的进程产生补充已经退出的进程
 */
$workerNum = 10; //设置10个进程
$pool = new Swoole\Process\Pool($workerNum,SWOOLE_IPC_UNIXSOCK, 0, true);
$pool->on("WorkerStart", function (Swoole\Process\Pool $pool, $workerId) {
    //$process = $pool->getProcess();
    echo "time: ".time()."; workID: '.$workerId\n";
    $redis = new Redis();
    $redis->connect('192.168.71.141',7001);
    $redis->auth('yis@2019._');
    $redis->select(9);
    $key = "key_queue";
    $msgs = $redis->lPush($key, $workerId);
    var_dump($msgs);
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();
