<?php
/**
 * 直接消费死信队里里的消息
 * 执行顺序：先运行消费者 然后在运行生产者 因为死信交换机及死信队列是在消费者中创建和声明的
 */
ini_set("display_errors","1");//开启报错日志
error_reporting(-1);        //所有报错
//www.try.com/php/rabbitmq/dead_letter_exchange/customer.php
header('Content-Type: text/html; charset=utf-8');
$conConfig = [
    'host' => '127.0.0.1',
    'port' => 5672,
    'login' => 'mandelay',
    'password' => '2018***',
    'vhost' => 'test_host'
];

while (true){
    try {
        //创建连接
        $con = new AMQPConnection($conConfig);
        $con->connect();
        if (!$con->isConnected()) {
            echo '连接失败';
            die;
        }

        //根据连接创建通道 不设置预取数量时 工作队列模式下（一个队列有多个消费者） 采用的是轮询机制，高并发情况下,消息是完全平均分发给每个消费者
        $channel = new AMQPChannel($con);

        //通过通道获取默认交换机
        $exchange = new AMQPExchange($channel);
        $exchange_name = "dlx_exchange";
        $exchange->setName($exchange_name);        // 设置交换机名称
        $exchange->setType( AMQP_EX_TYPE_DIRECT);   // 设置交换机类型
        $exchange->setFlags(AMQP_DURABLE);                    // 持久化 即使重启数据依旧存在
        $exchange->declareExchange();                               //  声明此交换机

        //创建及声明一个队列作为死信对列  正常业务处理队列过期的消息会通过死信交换机发送给该队列
        $queue_name = 'dead_letter_queue';
        $queue    = new AMQPQueue($channel);
        $queue->setName($queue_name);                       // 队列名称
        $queue->setFlags(AMQP_DURABLE);               // 持久化 即使重启数据依旧存在
        $queue->declareQueue();                             // 声明此队列

        //将队列、交换机、rounting-key 三者绑定
        $routing_key = "dead_letter_key";
        $queue->bind($exchange_name, $routing_key);                 // 将队列、交换机、rounting-key 三者绑定

        //获取队列里的消息进行消费处理 手动发送ack确认(AMQP_NOPARAM)  确认已收到消息，并把消息从队列中移除
        $queue->consume(function ($envelope, $queue) {
            $msg = $envelope->getBody();
            file_put_contents('./mq_2.txt',json_encode($msg).PHP_EOL,FILE_APPEND);
            $queue->ack($envelope->getDeliveryTag());
        },AMQP_NOPARAM);
        $con->disconnect();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

