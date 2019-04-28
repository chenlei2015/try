<?php
//扫码支付模式二（常用）
//24小时内未支付的订单 要系统自动帮用户取消 定时任务执行

include ('function.php');

include ('config.php');

include ('Payment.php');

include ('qrcode.php');

include ('order.php');

$params = [
    'body' =>'防水剃须刀',
    'detail' => '限时优惠',
    'out_trade_no' => time().mt_rand(10000,20000),//商城系统中唯一性 订单号
    'total_free' =>10000, //单位分 100元  支付宝的单位为元
    'notify_url' => 'notify_url.php',//异步接收微信支付结果通知的回调地址
  //'open_id' => 'jdfgjdflgjdflllf' //可选 当你需要限定只有该open_id的用户才能支付时 就添加此参数
    'spbill_create_ip' =>get_client_ip(),//获取客服端IP地址 即下单用户的电脑的ip
    'app_id' => APP_ID,
    'mch_id' => MCH_ID,
    'trade_type' => 'NATIVE'
];

$order =new order();

$result=$order->prepare($params); //获取code_url

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="../js/jquery-1.9.0.min.js"></script>
</head>
<body>
<div>请扫码支付</div>
<div><img src="<?php echo qrcode($result['code_url']) //此为支付二维码 ?>" alt=""></div>
<input id="out_trade_no" type="hidden"  value="<?php echo $result['out_trade_no'] //商城系统订单号?>">
<script>>

    //js长轮询查询订单支付状态 如果订单状态为未支付 则一直轮询 如果为已支付 则停止轮询 跳转到订单支付成功页面提示用户支付成功
    $(function () {
        var t1;
        var sum = 0;
        var out_trade_no = $("#out_trade_no").val()

        t1 = setInterval("queryOrderStatus()",3000);

        function queryOrderStatus(){
            sum++;
            //如果查询次数多余600次就放弃
            if(sum>600){ window.clearInterval(t1);return false;}
            //如果查询次数大于180次后 就3000毫秒*10的时间间隔查询一次（30秒）
            if(sum>180){
                m=sum % 10;
                if(m!=0){return false;}
            }

            if(out_trade_no != ''){
               $.post('query_order.php',{'out_trade_no':out_trade_no},function(data){
                   if(data=="SUCCESS"){
                       window.location.href="跳转到支付成功提页面";
                   }
               })
            }
        }

    })

</script>
</body>
</html>

















