<?php
//PHP运行过程中出现 notice warning、fatal错误时 获取报错信息

// 错误处理函数
function customError($errno, $errstr)
{
    echo "<b>Error:</b> [$errno] $errstr<br>";
    echo "已通知网站管理员";
    // message_type 是0,发送信息到php.ini配置的 error_log 参数的文件中
    // message_type 是1,直接发送到邮箱,需要配置postfix和php.ini的sendmail
    // message_type 是3就发送到第三个参数指定的文件中
    // message_type 是4直接发送到 SAPI 的日志处理程序中,比如返回给了nginx,可以在nginx配置的error_log里看到。
    error_log("Error: [$errno] $errstr", $message_type=1, "someone@example.com", "From: webmaster@example.com");
}

// 设置错误处理函数
set_error_handler("customError");

//echo 5/0;

// 触发错误
$test = 2;
if ($test > 1) {
    trigger_error("变量值必须小于等于 1", E_USER_WARNING);
}

