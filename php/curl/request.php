<?php
require 'Curl.php';
$url = 'https://www.googleapis.com/oauth2/v4/token';
$curl = new Curl();
const CLIENT_ID = '824205327910-8lldk1bumj122tgcsk5u2a0prikc3pav.apps.googleusercontent.com';
const CLIENT_SECRET = 'SCWSJGJ9ng-NK8BwEbF-pcsi';
const REDIRECT_URI  = 'https://image-us.bigbuy.win/google/token';  #需要在开发者应用中配置该url
$auth_code = '4/fAGCKoFbqNIKIGp5fkplMG8F8WtocjulWiVqapI8GcRKAggIvoeyVgAMkqin5GK44iZKNJ4YdOjPxwzy2p1QGj4';
$param = array(
    'code'          => $auth_code,
    'client_id'     => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,
    'redirect_uri'  => REDIRECT_URI,
    'grant_type'    => 'authorization_code'
);

$header = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Type' => 'application/json'
);

$data_json = json_encode($param);

$result = $curl->requestCurl($url,$header,$data_json);

print_r($result);die;


