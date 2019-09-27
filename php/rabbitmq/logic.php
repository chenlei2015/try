<?php
class Logic{
    //消息处理逻辑
    public function msgHandle($msg){
        file_put_contents('./../../log/mq.txt',json_encode($msg).PHP_EOL,FILE_APPEND);
    }

}