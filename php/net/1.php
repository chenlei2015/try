<?php
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://udeskdemo8732.udesk.cn/open_api_v1/log_in",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_SSL_VERIFYPEER=>0,   //服务端不验证ssl证书
    CURLOPT_SSL_VERIFYHOST=>0,   //服务端不验证ssl证书
    CURLOPT_POSTFIELDS => "{\r\n    \"email\": \"udeskdemo@udesk.cn\",\r\n    \"password\": \"udesk51377\"\r\n}",
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "Postman-Token: c5a85648-3e5d-4309-a7ad-ebcc3d746b64",
        "cache-control: no-cache"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}



