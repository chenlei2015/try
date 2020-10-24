<?php
require 'Curl.php';
$curl = new Curl();
$url = "http://hq.sinajs.cn/list=sh519096";
$url = "http://www.tms-b.com/tracksys/logistics/logisticsPullConfig/index?__withList=logisticsList&page_size=1&page=1";
$result = $curl->requestByCurlGet($url);

print_r($result);die;

$data = json_decode($result,true);

file_put_contents('./1.php',"<?php\n".'return '.var_export($data['data'], true).";\n\n?>",FILE_APPEND);
