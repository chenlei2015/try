<?php
/**
 *  swoole 进程池
 *
 *  后台守护进程 模式运行
 *  /usr/local/php/bin/php /home/wwwroot/try/pool.php > /dev/null 2>&1 &
 *
 *  ps -ef | grep pool.php
 *
 *  ps -ef | grep ordersys*
 *
 *
 * /usr/local/php/bin/php /home/wwwroot/tms/appdal/index.php /ordersys/console/ShipCost/sync
 *
 */
//报错日志开启
ini_set("display_errors","1");//开启报错日志
error_reporting(-1);        //所有报错
//$workerNum = 5;
$workerNum = 10;
$pool = new Swoole\Process\Pool($workerNum,SWOOLE_IPC_UNIXSOCK, 0, true);
$pool->on("WorkerStart", function (Swoole\Process\Pool $pool, $workerId) {
    $process = $pool->getProcess();
    $path_entry = '/home/wwwroot/tms/appdal/index.php';
    //传递2个参数 'WYT_model'和 "1"
    //$process->exec("/usr/local/php/bin/php", [$path_entry, 'ordersys/console/ShipCost/getCost','WYT_model',"1"]);
    //$process->exec("/usr/local/php/bin/php", [$path_entry, 'ordersys/console/ShipCost/getCost','WYT_model']); // 拉去万邑通财务费用项数据
    $process->exec("/usr/local/php/bin/php", [$path_entry, '/ordersys/console/ShipCost/sync']);    // 包裹重量回推给物流商
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();
