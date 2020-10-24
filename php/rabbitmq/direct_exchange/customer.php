<?php
//bin/php  D:\code\phpstudy\PHPTutorial\WWW\try\php\rabbitmq\direct_exchange\customer.php   已守护进程的模式运行在服务端
require "rabbitmq.php";
require "logic.php";
$queueName = "test_upload_data";
$rabbitmq = new RabbitMQ();
$googleSyncStock = new Logic();
while(true) {
    $rabbitmq->receiveMessage([$googleSyncStock, 'msgHandle'], $queueName);
    sleep(2);
}


