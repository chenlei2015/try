<?php

class webSocket{

    const IP = "0.0.0.0";
    const PORT = 9504;

    private $ws;

    public function __construct()
    {
       //创建websocket服务器对象，监听0.0.0.0:9504端口
       $this->ws = new swoole_websocket_server(self::IP,self::PORT);

       $this->ws->set([
            'worker_num' => 2,
            'task_worker_num' => 4,
       ]);

       //监听WebSocket连接打开事件
       $this->ws->on('open',[$this,'onOpen']);

       //监听WebSocket消息事件
       $this->ws->on('message',[$this,'onMessage']);

       //监听投递的异步任务
       $this->ws->on('task',[$this,'onTask']);

        //监听投递的异步任务
       $this->ws->on('finish',[$this,'onFinish']);

       //监听WebSocket连接关闭事件
       $this->ws->on('close',[$this,'onClose']);

       //开启服务
       $this->ws->start();
    }


    public function onOpen($ws, $request){
        echo"客服端与服务端已建立链接_".$request->fd;
    }


    public function onMessage($ws, $frame){

        $data=[
            'msg'=>$frame->data."_task",
            'frame_id' => $frame->fd
        ];

        echo "开始任务投递";

        $ws->task($data);//投递一个异步任务到task_worker进程池中 触发服务的task事件

        $ws->push($frame->fd, "server: {$frame->data}");
    }


    public function onTask($ws, $task_id, $from_id, $data){
        sleep(10);
        echo '处理异步投递的任务';
        $ws->finish($data);//触发服务的finish事件
    }


    public function onFinish($ws, $task_id, $data){
        echo "异步投递的任务处理完成";
        $ws->push($data['frame_id'], "server: {$data['msg']}");//向客户端发送消息
    }


    public function onClose($ws, $frame){
            echo "客服端与服务端链接断开";
    }


}


$ws = new webSocket();
