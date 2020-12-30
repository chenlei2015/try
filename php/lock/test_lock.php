<?php
define('APPPATH','../try/log/');
require "Lock.php";
file_put_contents(APPPATH.'/lock.txt',"110 start fpid:".posix_getpid().PHP_EOL.PHP_EOL,FILE_APPEND);
/**
 * 悲观锁测试
 */
function test(){
    file_put_contents(APPPATH.'/lock.txt',"111 start fpid:".posix_getpid().PHP_EOL.PHP_EOL,FILE_APPEND);
    $lock = new Lock();
    $key  = "secKill";
    //获取锁
    if($lock->lock($key,10,2)){
        //假设某业务处理耗时过长,发生阻塞30s
        for($i=0;$i<30;$i++){
//            if($i == 45){
//                file_put_contents(APPPATH.'/lock.txt'," 主进程意外死亡: time: ".microtime().PHP_EOL.PHP_EOL,FILE_APPEND);
//                die();
//            }
            sleep(1);
        }
        //业务执行完毕释放锁
        $lock->unlock($key);
    }
}
test();
