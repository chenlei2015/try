<?php
function test(){
    $pid = pcntl_fork();
    $n = "start559954";
    if($pid ==-1 ){
        die();
    }elseif ($pid){
        sleep(5);
        file_put_contents('./f_pid.txt', microtime().PHP_EOL,FILE_APPEND);
        die();
    }else{
        sleep(10);
        file_put_contents('./s_pid.txt', microtime().PHP_EOL,FILE_APPEND);
    }
    pcntl_waitpid($pid);

}

echo  test();