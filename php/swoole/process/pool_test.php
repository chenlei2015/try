<?php
require "../../curl/Curl.php";
$pool = new Swoole\Process\Pool(10, 0, 0);
$pool->on('workerStart', function (Swoole\Process\Pool $pool, int $workerId) {
    $curl = new Curl();
    Swoole\Timer::tick(1000, function (int $timer_id) use ($curl) {
        $url ="www.tms-b.com/ordersys/console/ShipCost/getQueue";
        $curl->requestByCurlGet($url);
    });
});
$pool->start();
