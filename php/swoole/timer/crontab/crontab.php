<?php

/**
 * sw crontab.php
 */

require './timer.php';

//$jobs[0] = new timer("0,30 18-23 * * * index.php");
$jobs[0] = new timer("* * * * *","ordersys/console/ShipCost/getQueue");

swoole_timer_tick(1000, function($timeId, $params = null) use ($jobs, &$prevTime) {
    $current = time();
    //week, month, day, hour, min
    $ref = explode('|', date('w|n|j|G|i', $current));
    if ($prevTime) {
        $prevMin  = date('i', $prevTime);
        if ($prevMin == $ref[4]) {
            return true;
        }
    }
    foreach ($jobs as $task) {
        $ready = 0;
        //$diff = $task->getTimeAttribute('runTime') - $current;
        //对应上面的$ref数组
        foreach (['week', 'month', 'day', 'hour', 'min'] as $key => $field) {
            $value = $task->getTimeAttribute($field);
            if ($value === '*') {
                $ready += 1;
                continue;
            }
            $ready += in_array($ref[$key], $value) ? 1: 0;
        }
        if (5 === $ready) {
            //执行任务
            $task->run_curl();
            //$task->run_cli();
            //更新运行时间
            $task->setRunTime($current);
        }
    }
    $prevTime = $current;
    return true;
    //swoole_timer_clear($timeId);
});


