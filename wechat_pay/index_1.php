<?php

include ('function.php');

include ('config.php');

include ('Payment.php');

include ('qrcode.php');

//扫码支付模式一
$product_id=10000;

$payment = new Payment();

$url =$payment->generateScanUrl($product_id); //调用生成二维码url的方法

//支付sdk里面的类 qrcode为生成二维码的类

qrcode::png($url);








