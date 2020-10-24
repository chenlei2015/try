<?php
//www.try.com/php/rabbitmq/default_exchange/customer_one.php
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
        $channel->setPrefetchCount(1); //设置预取消息个数为1， 工作队列模式下（一个队列有多个消费者） 确保每个消费者从同一个队里消费的消息数量一样多

        //根据通道创建并指明要消费的队列
        $queue_name = 'test.queue1';
        $queue = new AMQPQueue($channel);
        $queue->setName($queue_name);

        //获取队列里的消息进行消费处理   不发送ack确认(AMQP_NOPARAM)  不确认已收到消息，不把消息从队列中移除
        $queue->consume(function ($envelope, $queue) {
                sleep(3);
                $msg = $envelope->getBody();
                //$queue->nack($envelope->getDeliveryTag());
                //$queue->nack($envelope->getDeliveryTag(), AMQP_REQUEUE);
                file_put_contents('./../../../log/mq_1.txt',json_encode($msg).PHP_EOL,FILE_APPEND);
        });

        $con->disconnect();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}