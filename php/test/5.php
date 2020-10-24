<?php
require "../curl/Curl.php";
$url = 'www.tms-b.com/ordersys/api/WarehouseAgeFeeList/exportToFile';
$curl = new Curl();

$header = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Type' => 'application/json'
);

$data_json = '{"request_params":{"list_type":"1","charge_date_start":"2020-06-12","charge_date_end":"2020-06-12"},"request_type":"1","user_id":"1678"}';

$result = $curl->requestByCurlPost($url,$header,$data_json);

print_r($result);die;
