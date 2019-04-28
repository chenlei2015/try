<?php

ini_set("display_errors", "On");
error_reporting(-1);

$file_content = date();
swoole_async_writefile(__DIR__.'/write.txt', $file_content, function($filename) {
    echo "wirte ok.\n";
}, $flags = 0);

echo 99999999;