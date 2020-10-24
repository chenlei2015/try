<?php
// www.try.com/php/rabbitmq/header_exchange/customer_two.php
// php  D:\code\phpstudy\PHPTutorial\WWW\try\php\rabbitmq\header_exchange\customer_two.php
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

        //根据连接创建通道
        $channel = new AMQPChannel($con);

        //根据通道创建并指明要消费的队列
        $queue_name_two = 'test.queue2';
        $queue_two = new AMQPQueue($channel);
        $queue_two->setName($queue_name_two);

        //获取队列里的消息进行消费处理   发送ack自动确认(AMQP_AUTOACK)  确认已收到消息，把消息从队列中移除
        $queue_two->consume(function ($envelope, $queue) {
            $msg = $envelope->getBody();
            file_put_contents('./../../../log/mq_2.txt',json_encode($msg).PHP_EOL,FILE_APPEND);
        }, AMQP_AUTOACK);

        $con->disconnect();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
