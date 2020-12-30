<?php
ignore_user_abort(true);  // 浏览器关闭的情况下 可以继续运行PHP
set_time_limit(0);     //永不超时
/**
 * RabbitMQ死信机制实现延迟队列
 * 实现的方式有两种：
 * 通过消息过期后进入死信交换器，再由交换器转发到延迟消费队列，实现延迟功能；
 * 使用rabbitmq-delayed-message-exchange插件实现延迟功能；
 *
 * 说明：
 * 消息的TTL就是消息的存活时间。RabbitMQ可以对队列和消息分别设置TTL。
 * 对队列设置就是队列没有消费者连着的保留时间，也可以对每一个单独的消息做单独的设置。
 * 超过了这个时间，我们认为这个消息就死了，称之为死信。如果队列设置了，消息也设置了，那么会取小的。
 * 所以一个消息如果被路由到不同的队列中，这个消息死亡的时间有可能不一样（不同的队列设置）。
 * 这里单讲单个消息的TTL，因为它才是实现延迟任务的关键。
 *
 * 执行顺序：
 * 先运行消费者 然后在运行生产者 因为死信交换机及死信队列是在消费者中创建和声明的
 */
//www.try.com/php/rabbitmq/dead_letter_exchange/product.php
header('Content-Type: text/html; charset=utf-8');
// 连接设置
//192.168.31.16:15672
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

    //通过通道获取默认交换机
    $exchange = new AMQPExchange($channel);
    $exchange_name = 'normal_business_handle_exchange';
    $exchange->setName($exchange_name);        // 设置交换机名称
    $exchange->setType( AMQP_EX_TYPE_DIRECT);   // 设置交换机类型
    $exchange->setFlags(AMQP_DURABLE);                   //  持久化 即使重启数据依旧存在
    $exchange->declareExchange();                               //  声明此交换机

    //创建及声明正常的业务处理队列
    $queue_name = 'normal_business_handle_queue';
    $queue      = new AMQPQueue($channel);
    $queue->setName($queue_name);                       // 队列名称
    $queue->setFlags(AMQP_DURABLE);               // 持久化 即使重启数据依旧存在

    //为该队列关联死信交换机及死信交换机rounting-key而添加的必须设置的参数
    $queue->setArgument('x-message-ttl',10000); //设置队列的过期时间是10秒 队列过期后  队列中的消息会由死信交换机跟据死信路由发送给绑定的死信队列
    $queue->setArgument('x-dead-letter-exchange','dlx_exchange');//设置该队列关联的死信交换机
    $queue->setArgument('x-dead-letter-routing-key','dead_letter_key');//设置死信路由key
    $queue->declareQueue(); // 声明此队列

    //将队列、交换机、rounting-key 三者绑定
    $routing_key = "normal_business_key";
    $queue->bind($exchange_name, $routing_key);                 // 将队列、交换机、rounting-key 三者绑定

    for ($i = 1; $i <= 6; $i++) {
        $message = [
            'name' => '默认交换机，消息-' . $i,
            'info' => 'Hello World!'
        ];
        // 发送消息，为消息指定routing key，成功返回true，失败false
        if(($i%2)==1){
            //过期时间为10秒 遵循对队列设置的过期时间  这种可以
            $state = $exchange->publish(json_encode($message, JSON_UNESCAPED_UNICODE), $routing_key);
        }else{
            //单独为某个消息设置过期时间 每个消息的过期时间可以不一样  如果队列设置过期时间，消息也设置了过期时间，那么会取小的  理论是这样好像不起作用
            $state = $exchange->publish(json_encode($message, JSON_UNESCAPED_UNICODE), $routing_key,AMQP_NOPARAM,['expiration'=>1000*$i]);
        }

        if ($state) {
            echo 'Success' . PHP_EOL;
        } else {
            echo 'Fail' . PHP_EOL;
        }
        sleep(10);
    }
    // 关闭连接
    $con->disconnect();
} catch (Exception $e) {
    echo $e->getMessage();
}