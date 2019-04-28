<?php
$request = new HttpRequest();
$request->setUrl('https://xxxx.udesk.cn/open_api_v1/log_in');
$request->setMethod(HTTP_METH_POST);
$request->setHeaders(array(
    'Postman-Token' => '01dead15-648a-4b0e-828c-84bb2a806f57',
    'cache-control' => 'no-cache',
    'Content-Type' => 'application/json'
));

$request->setBody('{
    "email": "admin@email.com",
    "password": "xxxxpassword"
}');

try {
    $response = $request->send();
    echo $response->getBody();
} catch (HttpException $ex) {
    echo $ex;
}