<?php
file_put_contents('./lock.txt',"110 start fpid:".posix_getpid().PHP_EOL.PHP_EOL,FILE_APPEND);
$pm = new Swoole\Process\Manager();
//Manager进程
$pm->addBatch(1,function (Swoole\Process\Pool $pool, int $workerId){
    //工作进程  当工作进程执行完毕后  Manager进程会自动重新进行addBatch 周而复始 循环下去
    file_put_contents('./lock.txt',"110 start fpid:".posix_getpid().PHP_EOL.PHP_EOL,FILE_APPEND);
    $process = $pool->getProcess();
    $process->exec('/usr/local/php/bin/php', array(__DIR__.'/php/lock/test_lock.php'));
});
$pm->start();
