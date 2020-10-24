<?php
/**
 * 直连交换机
 * 显示声明路由及交换机
 */
//www.try.com/php/rabbitmq/direct_exchange/product.php
require "rabbitmq.php";
$message ='{"available_stock":2,"on_way_stock":1,"sku":"JY18009-013","warehouse_code":"CKY_AU","type":"add"}';
$queueName = "test_upload_data";
$key_route ="test_key_route";
$exchange_name = "test_exchange_name";
$rabbitmq = new RabbitMQ();
$rabbitmq->sendMessage($message,$queueName,$key_route,$exchange_name);

$rabbitmq_1 = new RabbitMQ();
$rabbitmq_1->sendMessage($message,$queueName,'test_key_route_1',$exchange_name);



