<?php
/**
 * 处理微信结果通知
 * 1.如何调试（postman,记录日志）
 * 2.安全性_1 微信给我们的通知结果 我们只需处理一次
 * 3.安全性—2 防止别人伪造微信服务器发送结果通知给我们 我们要进行签名验证 并校验返回的订单金额是否与商户侧的订单金额一致 避免不必要的损失
 */
include('function.php');

//1.调试 记录日志
$info= file_get_contents('php://input'); //用PHP协议获取输入流  用get,post也行
logInfo($info);//把微信支付传给这个接口的数据 记录到日志当中 调试的时候用 或者也可以在生产环境保留

//2.测试从微信服务支付器接收到的数据
$order = xmlToArray($info);
$trade['order_sn'] = $order['out_trade_no'];
$trade['total_free'] = $order['total_free'];

//签名的验证
$mySign= generateSign($order,KEY);

//订单金额的验证 到数据库中查找该订单的订单支付金额
$order_amount=Order::find()->select('pay_amount')->where(['order_sn'=>$trade['order_sn']])->one();

if($order_amount==$trade['total_free'] && $mySign == $order['sign']){

    if($order_status=="已支付"){
        //已支付不做任何处理 直接return就行了
        return;
    }else{
       //todo:如果订单状态为未支付 更改订单状态为已支付和其他的逻辑处理
    }

    //通知微信支付服务器结束回调通知
    return toXml(['return_code'=>'SUCCESS','return_msg'=>'OK']);

}
















