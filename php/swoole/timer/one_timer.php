<?php

/**
 * 执行并传递参数
 * D:\code\sw\php-cli\bin/php one_timer.php -a 7 -b 9
 * D:\code\sw\php-cli\bin/sw one_timer.php -a 7 -b 9
 *
 * kill -9  pid
 * dos命令杀死进程命令
 * taskkill /F /im php.exe
 * taskkill /F /im php-cgi.exe
 * taskkill /F /im sw.exe
 *
 *
 * ps -auxf | grep "php-fpm"
 *
 * 树状查看
 * pstree -hap 16791（父进程ID）
 *
 * dos命令查找进程命令
 * tasklist | findstr  php.exe
 * tasklist | findstr  php-cgi.exe
 * tasklist | findstr  sw.exe
 */
require "../../curl/Curl.php";

$curl         = new Curl();
$params       = getopt('a:b:');
$ship_company = $params['a'] = 4;
$counter = 0;
$process = new \Swoole\Process(function (\Swoole\Process $process) use ($ship_company, $curl) {
    //一分钟以后才会 发出请求
    Swoole\Timer::tick(1000, function (int $timer_id) use ($ship_company, $process, $curl) {
        //$url ="http://www.tms-b.com/cargoapisys/api/Demo/wet?debug=y";
        //$url ="http://192.168.71.141:92/ordersys/console/ShipFee/pullTailData";
        //$url = "http://192.168.71.141:92/ordersys/console/ShipFee/getTailFee";
        //$url ="http://192.168.71.141:92/ordersys/console/ShipFee/pullHeaderData";
        //$url ="http://192.168.71.141:92/ordersys/console/ShipFee/getHeaderFee";
        $url ="http://192.168.31.29:92/ordersys/console/ShipCost/getCost?model=WYT_model";
        $curl->requestByCurlGet($url);
    });
});
$process->start(); // 启动子进程

