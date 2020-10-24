<?php
/**
 * 扇形交换机
 * 该模式下的交换机是广播模式, 交换机会向所有绑定的队列分发消息, 不需要设置交换机和队列的 routing key. 即使设置了, 也会被忽略.
 */
//www.try.com/php/rabbitmq/default_exchange/product.php
header('Content-Type: text/html; charset=utf-8');
// 连接设置
$conConfig = [
    'host' => '127.0.0.1',
    'port' =>  5672,
    'login' => 'mandelay',
    'password' => '2018***',
    'vhost' => 'test_host'
];

try {
    // RabbitMQ 连接实例
    $con = new AMQPConnection($conConfig);
    // 发起连接
    $con->connect();
    // 判断连接是否仍然有效
    if (!$con->isConnected()) {
        echo '连接失败';
        die;
    }
    // 新建通道
    $channel = new AMQPChannel($con);

    //通过通道创建及声明扇形交换机(广播模式)
    $exchange_name = 'test_fanout';         //交换机名称
    $exchange = new AMQPExchange($channel);
    $exchange ->setName($exchange_name);
    $exchange ->setType(AMQP_EX_TYPE_FANOUT);
    $exchange ->setFlags(AMQP_DURABLE);
    $exchange->declareExchange();

    //创建及声明队列一
    $queue_name_one = 'test.queue1';
    $queue_one    = new AMQPQueue($channel);
    $queue_one->setName($queue_name_one);
    $queue_one->setFlags(AMQP_DURABLE);
    $queue_one->declareQueue();
    $queue_one->bind($exchange_name);  // 绑定队列到交换机。Fanout模式下不需要指定routing key，即使指定也会被忽略

    //创建及声明队列二
    $queue_name_two = 'test.queue2';
    $queue_two    = new AMQPQueue($channel);
    $queue_two->setName($queue_name_two);
    $queue_two->setFlags(AMQP_DURABLE);
    $queue_two->declareQueue();
    $queue_two->bind($exchange_name); // 绑定队列到交换机。Fanout模式下不需要指定routing key，即使指定也会被忽略

    for ($i = 1; $i <= 6; $i++) {
        $message = [
            'name' => '默认交换机，消息-' . $i,
            'info' => 'Hello World!'
        ];
        // 发送消息，Fanout模式下不需要指定routing key，即使指定也会被忽略
        $state = $exchange->publish(json_encode($message, JSON_UNESCAPED_UNICODE));
        if ($state) {
            echo 'Success' . PHP_EOL;
        } else {
            echo 'Fail' . PHP_EOL;
        }
    }
    // 关闭连接
    $con->disconnect();
} catch (Exception $e) {
    echo $e->getMessage();
}