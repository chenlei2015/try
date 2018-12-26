<?php
/**
 * 非对称加密
 */
//1.资源配置
$config = array(
    'config'=>'E:\software\xampp\php\extras\openssl\openssl.cnf',
    "digest_alg" => "sha512",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

//2.根据创建一个私钥资源
$res = openssl_pkey_new($config);

//3.获取私钥$priKey
openssl_pkey_export($res, $priKey,null, $config);
var_dump($priKey);
//4.获取公钥$pubKey
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey["key"];
var_dump($pubKey);
//5.需要加密的字符串
$data = 'plaintext data goes here';

//6.使用公钥加密字符串 获取加密后的字符串$encrypted
openssl_public_encrypt($data, $encrypted, $pubKey);

//7.使用私钥解密字符串 获取解密后的字符串$decrypted
openssl_private_decrypt($encrypted, $decrypted, $priKey);

var_dump($decrypted);