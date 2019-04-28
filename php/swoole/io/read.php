<?php
ini_set("display_errors", "On");
error_reporting(-1);

Swoole\Async::readFile(__DIR__."/read.txt",function ($filename, $content){
     echo $filename.' : '.$content;
});

echo 888888;