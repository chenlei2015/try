<?php
/**
 * 查询订单支付状态
 */

include ('function.php');

include ('config.php');

include ('Payment.php');

include ('order.php');


$params = [
    'app_id' => APP_ID,
    'mch_id' => MCH_ID,
    'out_trade_no' =>$_POST['out_trade_no']
];

$order =new order();

$result=$order->query_order($params); //获取code_url

logInfo('query_result: '.json_encode($result),'qurey');//把查询结果写到日志中

if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){//返回查询结果
    echo $result['trade_state'];
}else{
    echo "FAIL";
}


