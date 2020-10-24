<?php
/**
 *    此模式下交换机，在推送消息时, 会根据消息的主题词和队列的主题词决定将消息推送到哪个队列. 交换机只会为 Queue 分发符合其指定的主题的消息。
 *
 *   1.向交换机发送消息时，消息的 routing key 就是主题关键词，主题词不能随意设置，必须由 "." 联结多个主题词 (如：log.error、log.warn) .
 *   2.必须将队列显示的绑定到指定的交换机上.
 *   2.为队列指定队列主题词时，可以使用通配符: "#": 表示 0 或多个主题词; "*": 表示 1 个主题词. 主题词不是指一个字符 是指两个点之间的一个单词  例如 quick.orange.rabbit 有三个主题词
 *
 */
//www.try.com/php/rabbitmq/default_exchange/product.php
header('Content-Type: text/html; charset=utf-8');
// 连接设置
$conConfig = [
    'host' => '127.0.0.1',
    'port' => 5672,
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

    //通过通道创建及声明主题交换机(主题模式)
    $exchange_name = 'test_topic';         //交换机名称
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchange_name);
    $exchange->setType(AMQP_EX_TYPE_TOPIC);
    $exchange->setFlags(AMQP_DURABLE);
    $exchange->declareExchange();

    //创建及声明队列一
    $queue_name_one = 'test.queue1';
    $queue_one = new AMQPQueue($channel);
    $queue_one->setName($queue_name_one);
    $queue_one->setFlags(AMQP_DURABLE);
    $queue_one->declareQueue();
    $routing_key_pattern = "*.yh.#";//路由匹配模式
    //绑定队列到交换机。topic模式下需要指定routing key的匹配模式，而不是具体的routing key值 (这里的匹配模式相当于正则表达式,只要所发送消息的routing key符合这个匹配模式，交换机就会把该消息发送到当前绑定的队列中)，
    $queue_one->bind($exchange_name, $routing_key_pattern);


    //创建及声明队列二
    $queue_name_two = 'test.queue2';
    $queue_two = new AMQPQueue($channel);
    $queue_two->setName($queue_name_two);
    $queue_two->setFlags(AMQP_DURABLE);
    $queue_two->declareQueue();
    $routing_key_pattern = "green.*"; //路由匹配模式
    //绑定队列到交换机。topic模式下需要指定routing key的匹配模式，而不是具体的routing key值 (这里的匹配模式相当于正则表达式,只要所发送消息的routing key符合这个匹配模式，交换机就会把该消息发送到当前绑定的队列中)，
    $queue_two->bind($exchange_name, $routing_key_pattern);

    for ($i = 1; $i <= 60; $i++) {
        $color = $i % 2 == 0 ? "red" : "green";
        $message = [
            'name' => '默认交换机，消息-' . $i . "_" . $color,
            'info' => 'Hello World!'
        ];
        // 发送消息，topic模式下需要指定routing key的具体值
        $routing_key = $i % 2 == 0 ? "red.yh." . $i : "green.yh.jk." . $i;
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