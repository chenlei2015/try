<?php
$serv = new swoole_server("127.0.0.1", 9510);

//设置异步任务的工作进程数量
$serv->set(array('task_worker_num' => 4));

$serv->on('receive', function($serv, $fd, $from_id, $data) {
    //投递异步任务
    echo "data_1:$data\n";
    $task_id = $serv->task($data);
    echo "Dispath AsyncTask: id=$task_id\n";
});

//处理异步任务
$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    sleep(2);
    echo "New AsyncTask[id=$task_id]".PHP_EOL;
    //返回任务执行的结果
    $serv->finish("$data -> OK");
    echo "data_2:$data\n";
});

//处理异步任务的结果
$serv->on('finish', function ($serv, $task_id, $data) {
    sleep(2);
    echo "data_3:$data\n";
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});

$serv->start();