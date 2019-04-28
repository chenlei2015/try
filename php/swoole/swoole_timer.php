<?php
ini_set("display_errors", "On");
error_reporting(-1);
//swoole定时器

//每隔2000ms触发一次 swoole_timer_tick函数就相当于setInterval，是持续触发的
swoole_timer_tick(2000, function ($timer_id) {
    echo date("Y-m-d H:i:s",time())."\n";
});

