<?php

$http = new swoole_http_server("0.0.0.0", 9503);

$http->on('request', function ($request, $response) {
    var_dump($request->get, $request->post);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();




//telnet 模仿 http协议的get请求
//GET /php/swoole/http_server.php?name=test1&pwd=123456 HTTP/1.1
//HOST:www.try.com