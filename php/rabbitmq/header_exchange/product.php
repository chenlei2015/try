<?php
/**
 *    www.try.com/php/rabbitmq/header_exchange/product.php
 *    此模式下交换机，在推送消息时, 会根据消息的主题词和队列的主题词决定将消息推送到哪个队列. 交换机只会为 Queue 分发符合其指定的主题的消息。
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

    //通过通道创建及声明主题交换机(头部模式)
    $exchange_name = 'test_header';         //交换机名称
    $exchange = new AMQPExchange($channel);
    $exchange->setName($exchange_name);
    $exchange->setType(AMQP_EX_TYPE_HEADERS);
    $exchange->setFlags(AMQP_DURABLE);
    $exchange->declareExchange();

    //创建及声明队列一
    $queue_name_one = 'test.queue1';
    $queue_one    = new AMQPQueue($channel);
    $queue_one->setName($queue_name_one);
    $queue_one->setFlags(AMQP_DURABLE);
    $queue_one->declareQueue();

    //绑定队列到交换机。head模式需要给routing key指定一个值 (routing key的值 可以是任意值,包括null 一般指定和队列名一致) 但不起什么实际作用,
    //$routing_key = $queue_name_one;
    $routing_key = null;
    //设定队列的header信息，x-match：all 全匹配，消息的header信息与队列的header信息必须完全匹配
    $header_one = ['x-match'=>'all', 'type'=>'even', 'color'=>'red'];
    $queue_one->bind($exchange_name,$routing_key,$header_one);


    //创建及声明队列二
    $queue_name_two = 'test.queue2';
    $queue_two    = new AMQPQueue($channel);
    $queue_two->setName($queue_name_two);
    $queue_two->setFlags(AMQP_DURABLE);
    $queue_two->declareQueue();
    //绑定队列到交换机。head模式需要给routing key指定一个值 (routing key的值 可以是任意值,包括null 一般指定和队列名一致) 但不起什么实际作用,
    //$routing_key = $queue_name_two;
    $routing_key = null;
    //设定队列的header信息，x-match：any：消息的headers消息与队列header信息的任意一项匹配即可
    $header_two = ['x-match'=>'any', 'type'=>'odd', 'color'=>'red'];
    $queue_two->bind($exchange_name,$routing_key,$header_two);

    for ($i = 1; $i <= 10; $i++) {
        $message = [
            'name' => '默认交换机，消息-' . $i,
            'info' => 'Hello World!'
        ];
        //指定消息的header信息
        $message_header['headers'] = $i%2==0 ? ['type'=>'even','color'=>'green'] : ['type'=>'odd','color'=>'green'];
        $routing_key = null;
        $state = $exchange->publish(json_encode($message, JSON_UNESCAPED_UNICODE),$routing_key,AMQP_NOPARAM,$message_header);
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