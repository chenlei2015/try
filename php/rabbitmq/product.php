<?php
require "rabbitmq.php";
$queueName = "test_upload_data";
$message ='{"available_stock":2,"on_way_stock":1,"sku":"JY18009-013","warehouse_code":"CKY_AU","type":"add"}';
$rabbitmq = new RabbitMQ();
$rabbitmq->sendMessage($message,$queueName);

