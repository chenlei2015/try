<?php
require 'Curl.php';
$curl = new Curl();
$url = "http://hq.sinajs.cn/list=sh513050";
//$url = "http://192.168.71.170:90/product/product_display/get_collect_info?spu=27101900001";
$result = $curl->requestByCurlGet($url);

print_r($result);die;

$data = json_decode($result,true);

file_put_contents('./1.php',"<?php\n".'return '.var_export($data['data'], true).";\n\n?>",FILE_APPEND);
