<?php
/**
 * Created by PhpStorm.
 * User: mandelay
 * Date: 30/03/19
 * Time: 上午 10:23
 */

include('function.php');

class order {

    const  APL_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";//统一下单接口

    const   APL_QUERY = "https://api.mch.weixin.qq.com/pay/orderquery";//查询接口

    /**
     *
     * 调用微信统一下单接口获取获取预支付信息 其中code_url字符串用于生成支付二维码
     * @param $params
     */

    public function prepare($params){

        $params['nonce_str'] = uniqid();

        $params['sign'] = generateSign($params,KEY);

        $xml=toXml($params); //获取xml的请求参数

        $result = http_request(self::APL_URL,$xml);

        return xmlToArray($result);
    }


    public function query_order($params){

        $params['nonce_str'] = uniqid();

        $params['sign'] = generateSign($params,KEY);

        $xml=toXml($params); //获取xml的请求参数

        $result = http_request(self::APL_QUERY,$xml,false);

        return xmlToArray($result);
    }






}