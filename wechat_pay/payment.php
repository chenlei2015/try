<?php

class Payment{

    //获取用于生成二维码的url方法

    function generateScanUrl($product_id){

         $params= [
             'appid' => APP_ID,
             'mch_id' => MCH_ID,
             'nonce_str' => uniqid(),//php生成唯一字符窜函数
             'time_stamp'=> strval(time()),
             'product_id' =>$product_id
         ];

         $params['sign'] = generateSign($params,KEY);

         $prefix = "weixin：//wxpay/bizpayurl?";

         $url = $prefix.http_build_query($params);

         return $url;
    }

}