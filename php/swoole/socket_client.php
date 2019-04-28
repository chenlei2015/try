<?php
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <script src="../../js/jquery-1.9.0.min.js"></script>
</head>
<body>
<form name="talk">
    <textarea rows="5" cols="50" id="content" value=""></textarea>
</form>
<h3>WebSocket协议的客户端程序</h3>
<button id="btConnect">连接到WS服务器</button>
<button id="btSendAndReceive">向WS服务器发消息并接收消息</button>
<button id="btClose">断开与WS服务器的连接</button>
<div id="val"></div>
<script>
    var wsClient = null; //WS客户端对象

    // var content = document.getElementById('content').innerHTML;
    // console.log(content)

    btConnect.onclick = function(){
        //连接到WS服务器，注意：协议名不是http！
        wsClient = new WebSocket('ws://127.0.0.1:9504');
        //wsClient = new WebSocket('ws://127.0.0.1:9502');
        // wsClient.onopen = function(){
        //     console.log("已连接")
        // }
    }

    btSendAndReceive.onclick = function(){
        //用户输入消息
        var content=$("#content").val();
        //向WS服务器发送一个消息
        //wsClient.send('Hello Server');
        wsClient.send(content);
        //接收WS服务器返回的消息
        wsClient.onmessage = function(e){
            console.log('WS客户端接收到一个服务器的消息：'+ e.data);
            val.innerHTML=e.data;
        }


    }

    btClose.onclick = function(){
        //断开到WS服务器的连接
        wsClient.close();  //向服务器发消息，主动断开连接
        wsClient.onclose = function(){
            //经过客户端和服务器的四次挥手后，二者的连接断开了
            console.log('到服务器的连接已经断开')
        }
    }
</script>
</body>
</html>
