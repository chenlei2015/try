<?php
require "rabbitmq.php";
require "logic.php";
$queueName = "test_upload_data";
$rabbitmq = new RabbitMQ();
$googleSyncStock = new Logic();
while(true) {
    $rabbitmq->receiveMessage([$googleSyncStock, 'msgHandle'], $queueName);
    sleep(2);
}


