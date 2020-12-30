<?php
/**
 * 此种模式下，使用 RabbitMQ 的默认 Exchange 即可，默认的 Exchange 是 Direct 模式。
 * 使用默认 Exchange 时，不需要对 Exchange 进行属性设置和声明，也不需要对 Queue 进行显示绑定和设置 routing key。
 * Queue 默认会绑定到默认 Exchange，以及默认 routing key 与 Queue 的名称相同。
 */
//http://www.try.com/php/rabbitmq/dead_letter_exchange/product.php
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

    //通过通道获取默认交换机，使用RabbitMQ的默认Exchange时 无需进行命名、持久化、设置交换机类型（默认为直连交换机）、申明等操作
    $exchange = new AMQPExchange($channel);
    $exchange ->setFlags(AMQP_DURABLE);

    //创建及声明队列，不需要对Queue进行显示绑定到交换机和指定Queue的routing key 会默认绑定到默认交换机
    $queue_name = 'test.queue1';
    $queue    = new AMQPQueue($channel);
    $queue->setName($queue_name);
    $queue->declareQueue();

    for ($i = 1; $i <= 6; $i++) {
        $message = [
            'name' => '默认交换机，消息-' . $i,
            'info' => 'Hello World!'
        ];
        // 发送消息，为消息指定routing key，使用默认交换机时，routing key与队列名相同  成功返回true，失败false
        $routing_key = $queue_name;
        $state = $exchange->publish(json_encode($message, JSON_UNESCAPED_UNICODE), $routing_key);
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