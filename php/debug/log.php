<?php
// file_put_contents('./../../log/mq.txt',json_encode($msg).PHP_EOL,FILE_APPEND);
// $data 是个数组
$data = $_SERVER;
file_put_contents('./log.txt',"<?php\n".'return '.var_export($data, true).";\n\n?>",FILE_APPEND);