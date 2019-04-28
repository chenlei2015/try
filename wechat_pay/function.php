<?php

/**
 * 生成签名的方法
 * @param $params
 * @param $key
 * @param string $encrypt
 * @return string
 */
function generateSign($params,$key,$encrypt = 'md5'){

    //参数按照参数名ASCII码从小到大排序（字典序）
    ksort($params);

    //密钥
    $params['key']=$key;

    //生成请求字符窜
    $str = http_build_query($params);

    //MD5假名 生成签名
    return  strtoupper(md5(urlencode($str)));
}

/**
 * 获取客户端ip地址
 */

function get_client_ip(){

    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }elseif (!empty($_SERVER['REMOTE_ADDR'])){
        $ip = $_SERVER['REMOTE_ADDR'];
    }else{
        $ip = "无法获取";
    }

    return $ip;
}


/**
 * 把數組转换成微信支付要求的xml格式
 */
function toXml($params){

    $xml='<xml>';
    foreach ($params as $key => $val){

        if(is_numeric($val)){
            $xml.="<$key>$val</$key>";
        }else{
            $xml.="<$key><![CDATA[$val]]></$key>";
        }

    }
    $xml.='</xml>';

    return $xml;
}


/**
 * 把xml转换成数组
 */
function xmlToArray($data){

   return (array)simplexml_load_string($data,'SimpleXLMElement',LIBXML_NOCDATA);

}


/**
 * @param $url
 * @param string $data
 * @param bool $pem
 * @return bool|string
 */

function  http_request($url,$data='',$pem=false){

    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);//检查服务器SSL证书中是否存在一个公用名
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);//将执行curl_exec()获取的信息以字符串返回，而不是直接输出到屏幕。

    //设置请求方式及请求参数
    if(!empty($data)){
        curl_setopt($ch,CURLOPT_PORT, 1); //设置post提交方式
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data); //设置post方式提交的数据
    }

    //携带证书 微信支付商户中心下载的cert证书
    if($pem){
        curl_setopt($ch,CURLOPT_SSLCERT,  CERT_PATH);
        curl_setopt($ch,CURLOPT_SSLKEY,KEY_PATH );
        curl_setopt($ch,CURLOPT_CAINFO,CA_PATH );
    }

    //执行请求
    $result = curl_exec($ch);
    if($result === false){
        $result = "curl 错误信息： ".curl_error();
    }

    curl_close($ch);

    return $result;
}

/**
 * 日志记录
 */

function logInfo($info,$fileName="log"){

     $debugInfo= debug_backtrace();

     $message = date("Y-m-d H:i:s").PHP_EOL.$info.PHP_EOL;

     $message .= '[ '.$debugInfo[0]['file'].' ]line '.$debugInfo[0]['line'].PHP_EOL;

     $fileName = $fileName.'-'.date("Y-m-d").'.txt';

     file_put_contents($fileName,$message);
}







