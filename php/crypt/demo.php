<?php

$targetUrl	=	"https://api.jiaxincloud.com/rest/workgroup/token";

$ch	=	curl_init($targetUrl);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_HTTPGET, 1);
curl_setopt($ch,CURLOPT_TIMEOUT,30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL, 1);

$content = curl_exec($ch);
curl_close($ch);
       
echo $content;
echo "\r\n";
$content	=	json_decode($content);

$authstring = $content->www_Authenticate;

$auth = explode(",",$authstring); 

foreach ($auth as $value){
	 
	 if ( strstr($value,"nonce") ){
	 	   $Anonce = explode("=",$value);
	 	   $nonce  = str_replace("\"","",$Anonce[1]);
	 }
	 if ( strstr($value,"opaque") ){
	 	   $Aopaque = explode("=",$value);
	 	   $opaque  = str_replace("\"","",$Aopaque[1]);
	 }	 
}

echo $authstring;
echo "\r\n";
echo "nonce:" . $nonce;
echo "\r\n";
echo "opaque:" . $opaque;

$username = "fydceshi@qq.com";
$password = "jiaxin123";
$real     = "jiaxincloud.com";

$mdstr =  $username . ":" . $real . ":" . $password;

$ha1 = md5($mdstr);

echo "\r\n";
echo "ha1:". $ha1;

$mdstr =  "GET:" . "https://api.jiaxincloud.com/rest/workgroup/token";

$ha2 = MD5($mdstr);

echo "\r\n";
echo "ha2:". $ha2;

$nc     = "00000001";
$cnonce=md5(time().mt_rand(10000,99999));//由我们定义，保证唯一性就可以了
//$cnonce = "ab1b2f2d-3b0a-4eba-bd93-d56b5879a139";
$qop    = "auth";

$mdstr =  $ha1 . ":" . $nonce . ":" . $nc . ":" . $cnonce . ":" . $qop . ":" . $ha2;

$response = MD5($mdstr);

echo "\r\n";
echo "response:". $response;


$tmp_value = "Digest username=\"".$username."\",realm=\"jiaxincloud.com\",nonce=\"" . $nonce;
$tmp_value = $tmp_value . "\",uri=\"" . $targetUrl . "\",qop=\"auth\",nc=\"" . $nc . "\",cnonce=\"" . $cnonce . "\",response=\"" . $response . "\",opaque=\"" .  $opaque . "\"";

echo "\r\n";
echo $tmp_value;
echo "\r\n";

//$headers = array('Authorization:' => $tmp_value);
$headers = array(
    'Authorization:'.$tmp_value
);

$ch	=	curl_init($targetUrl);
curl_setopt($ch,CURLOPT_HEADER,1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_HTTPGET, 1);
curl_setopt($ch,CURLOPT_TIMEOUT,30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);

curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL, 1);

$content = curl_exec($ch);
curl_close($ch);

echo "\r\n";
echo "content:". $content;
echo "\r\n";

$result = explode("\r\n",$content);

$content = $result[count($result) - 1];

$content	=	json_decode($content);


echo "\r\n";
$token = $content->token;
          
echo "\r\n";  
echo "token:" . $token;
echo "\r\n";    
        
?>