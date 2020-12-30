<?php
//www.try.com/php/rabbitmq/default_exchange/customer_two.php
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

        /*
         * 设置预取消息个数为1， 工作队列模式下（一个队列有多个消费者），表示RabbitMQ同一时间发给消费者的消息不超过一条。
         * 这样就能保证消费者在处理完某个任务，并发送确认信息后，RabbitMQ才会向它推送新的消息，在此之间若是有新的消息话，
         * 将会被推送到其它消费者，若所有的消费者都在处理任务，那么就会等待
         *
         * $channel->setPrefetchCount(1);
         */

        //根据通道创建并指明要消费的队列
        $queue_name = 'test.queue1';
        $queue = new AMQPQueue($channel);
        $queue->setName($queue_name);

        //获取队列里的消息进行消费处理 自动发送ack确认(AMQP_AUTOACK)  确认已收到消息，并把消息从队列中移除
        $queue->consume(function ($envelope, $queue) {
            $msg = $envelope->getBody();
            file_put_contents('./../../../log/mq_2.txt',json_encode($msg).PHP_EOL,FILE_APPEND);
        }, AMQP_AUTOACK);

        $con->disconnect();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

