<?php
class RabbitMQ{
    //消息队列服务器配置
    private static $config =array(
        'host' => '192.168.71.192',
        'port' =>  5672,
        'login' => 'mquser',
        'password' => 'mq2019***',
        //'vhost' => '/'
    );
    //MQ连接对象
    private static $rabbitClient = null;
    public function __construct()
    {
        $connection = new AMQPConnection(self::$config);
        $connection->connect();
        if($connection->isConnected()){
            self::$rabbitClient = $connection;
        }else{
            throw new Exception("连接失败");
        }
    }

    /**
     *  说明不需要以守护进程的方式在服务端运行
     *  发送消息 生产者
     * @param string $message 要发送的信息
     * @param string $queue_name 队列名称
     * @param string $key 路由KEY rounting-key
     * @param string $exchange_name 交换机名称
     * @return mixed
     */
    public function sendMessage($message, $queue_name = 'df_queue', $key = 'df_key', $exchange_name = 'yb_erp_dc')
    {
        //检测是否连接
        if(is_null(self::$rabbitClient)){
            throw new Exception("连接MQ失败");
        }

        //重连机制
        if (self::$rabbitClient->isConnected() == false) {
            if (!self::$rabbitClient->reconnect()) {
                throw new Exception("重新连接MQ失败");
            }
        }

        try{
            //第一步创建信号通道
            $channel = new AMQPChannel(self::$rabbitClient);

            //第二步根据信号通道创建交换机
            $exchange = new AMQPExchange($channel);                   // 创建交换机
            $exchange->setName($exchange_name);                       // 设置交换机名称
            $exchange->setType( AMQP_EX_TYPE_DIRECT); // 设置交换机类型
            $exchange->setFlags(AMQP_DURABLE) ;                //  持久化 即使重启数据依旧存在
            $exchange->declareExchange();                             // 声明此交换机

            //第三步 根据信号通道创建队列
            $queue = new AMQPQueue($channel);                   // 创建队列
            $queue->setName($queue_name);                       // 队列名称
            $queue->setFlags(AMQP_DURABLE);               // 持久化 即使重启数据依旧存在
            $queue->declareQueue();                             // 声明此队列
            $queue->bind($exchange_name, $key);                 // 将队列、交换机、rounting-key 三者绑定

            //第四部将消息存入队列
            if (is_array($message) || is_object($message)) {    // 存入的消息数据一定是字符串
                $message = json_encode($message);
            }

            $result = $exchange->publish($message,$key);         //发送消息数据到队列
            if(!$result){
                throw new Exception('发送MQ消息失败');
            }
            return $result;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }


    /**
     * 说明：需要以守护进程的方式在服务端运行
     * 接收消息 消费者
     * @param funtion $callback 消息回调方法
     * @return [type] [description]
     */
    public function receiveMessage($callback,$queue_name = 'df_queue'){
        //检测是否连接
        if (is_null(self::$rabbitClient)) {
            throw new Exception("连接MQ失败");
        }
        //重连机制
        if (self::$rabbitClient->isConnected() == false) {
            if(!self::$rabbitClient->reconnect()){
                throw new Exception("重新连接MQ失败");
            }
        }
        //判断是否为回调函数
        if (empty($callback) || !is_callable($callback)) {
            throw new Exception("callback 必须是回调函数");
        }

        try{
            //第一步创建信号通道
            $channel = new AMQPChannel(self::$rabbitClient);

            //第二步 根据信号通道创建队列
            $queue = new AMQPQueue($channel);    // 创建队列
            $queue->setName($queue_name);        // 队列名称

            $queue->consume(function($envelope, $queue) use ($callback){
                $msg = $envelope->getBody ();               //拿出来的一定是字符串
                $reMsg = json_decode($msg,true);
                if(!is_null($reMsg)){
                    $msg = $reMsg;
                }

                $result = call_user_func ( $callback, $msg );
                if ($result) {
                    $queue->ack ($envelope->getDeliveryTag ());                //消息确认 手动确认已收到消息，并把消息从队列中移除
                }else{
                    //nack调用测试后，发现还是删除数据了，如果什么也不返回也不处理，数据会塞回队列,
                    //(但是会塞回队列的前面，不会放到后面，如果当前消费者线程没有关闭，那么那些未处理的数据谁也拿不到，包过当前消费者)
                    $queue->nack($envelope->getDeliveryTag());
                }
            });

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }

    }


    public function __destruct(){
        if(self::$rabbitClient){
            self::$rabbitClient->disconnect();
        }
        self::$rabbitClient = null;
    }

}