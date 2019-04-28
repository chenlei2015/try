 <?php
//配置数据
$appKey = "66fd1b23a8faa74e9e24bf56f6e4a744";
$appSecret = "BDF63451D04C451BB669D5AEF570190F";
$time=time();

//订单数据(加密数据)
$order=[
    ['uid'=>'uid1','amount'=>1,'orderTime'=>'1478826671000','orderId'=>'orderId1','payTime'=>'1478833871000','price'=>100],
    ['uid'=>'uid2','amount'=>2,'orderTime'=>'1478826881888','orderId'=>'orderId2','payTime'=>'1478833888888','price'=>213]
];
$data =strtolower(json_encode($order));


/**
 * 数据校验
 * @param $appSecret
 * @param $data  被校验的数据
 * @param $time  当前时间戳
 * @return string
 */
function getChecksum($appSecret,$data,$time){
    return sha1($appSecret.md5($data).$time);
}

/**
 * @param $data 被加密的数据
 * @param $secret_key 密钥
 * @param string $method  加解密方法，可通过openssl_get_cipher_methods()获得
 * @param int $options
 * @param string $iv
 * @return string 返回加密后的字符串
 */
function encrypt($data,$secret_key,$method='AES-128-ECB',$options=1,$iv=''){
    $secret_key= substr(openssl_digest(openssl_digest($secret_key, 'sha1', true), 'sha1', true), 0, 16);
    $data_encode = openssl_encrypt($data,$method,$secret_key,$options,$iv);
    return  Str2Ascii2hex($data_encode);
}

/**
 * @param $data 被解密的数据
 * @param $secret_key 密钥
 * @param string $method  加解密方法，可通过openssl_get_cipher_methods()获得
 * @param int $options
 * @param string $iv
 * @return string 返回解密后的字符串
 */
function decrypt($data,$secret_key,$method='AES-128-ECB',$options=1,$iv=''){
    $data=Str2Dec2chr($data);
    $secret_key= substr(openssl_digest(openssl_digest($secret_key, 'sha1', true), 'sha1', true), 0, 16);
    return openssl_decrypt($data,$method,$secret_key,$options,$iv);
}

/**
 * 把字符串转为16进制表示,其中ord函数将字符串中的每个字符转为对应的ASCII码,dechex 函数将ASCII码数字从十进制转为十六进制,然后链接成十六进制字符串
 * @param $str
 * @return string
 */
function Str2Ascii2hex($str){
    $str_hex='';
    for ($i=0;$i<strlen($str);$i++){
        $temp= ord($str[$i]);
        if ($temp<0){
            $temp+=256;
        }
        if($temp<16){
            $str_hex.='0';
        }
        $dechex=dechex($temp);
        $str_hex.=$dechex;
    }
    return $str_hex;
}

/**
 * 把16进制字符串的每两个字符转化为十进制 然后再把十进制的数字转化成字符 再然后把字符连接成字符串
 * @param $str
 * @return string
 */
function Str2Dec2chr($str){
    $str_dec='';
    for ($i=0;$i<strlen($str);$i+=2){
        $str_dec.=chr(hexdec(substr($str,$i,2)));
    }
    return $str_dec;
}


var_dump($data);
//加密
$data_encode=encrypt($data,$appSecret);
var_dump($data_encode);

//解密
$data_decode=decrypt($data_encode,$appSecret);
var_dump($data_decode);




/**
 * 请求方法
 * @return mixed|string
 */
$url="http://dfs02.qiyukf.com/openapi/message/send?appKey=".$appKey."&time=".$time."&checksum=".getChecksum($appSecret,$data,$time);
function requestByCurl($url){
    $ch=curl_init();
    curl_setopt_array($ch,[
        CURLOPT_URL =>$url,    //请求的url
        CURLOPT_RETURNTRANSFER =>1,  //不要把请求的结果直接输出到屏幕上
        CURLOPT_TIMEOUT =>30,        //请求超时设置
        CURLOPT_POST =>1,            //使用post请求此url
        CURLOPT_SSL_VERIFYPEER=>0,   //服务端不验证ssl证书
        CURLOPT_SSL_VERIFYHOST=>0,   //服务端不验证ssl证书
        CURLOPT_HTTPPROXYTUNNEL=>1,  //启用时会通过HTTP代理来传输
        CURLOPT_HTTPHEADER =>['content-type: application/json'],//请求头部设置
        CURLOPT_POSTFIELDS =>json_encode(['uid'=>'227899','msgType'=>'TEXT','content'=>'888888888888888'],JSON_UNESCAPED_UNICODE), //post请求时传递的参数
    ]);

    $content = curl_exec($ch);  //执行
    $err = curl_error($ch);
    curl_close($ch);
    if($err){
        return $err;
    }
    return json_decode($content);
}

var_dump(requestByCurl($url));




