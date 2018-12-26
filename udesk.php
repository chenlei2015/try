<?php
class Udesk{

      private  $url = "http://shenzhenudesk2018.udesk.cn/open_api_v1/log_in";

      private  $email = "shenzhenudesk@126.com";

      private  $password = "udesk123";

      private  $time;

      public function __construct($params)
      {
          $this->email=$params['email'];
          $this->password=$params['password'];
          $this->time=time();
      }

    /**
     * 获取open_api_token
     * @return mixed|string
     */
     private  function getToken(){
         $ch=curl_init();
         curl_setopt_array($ch,[
             CURLOPT_URL =>$this->url,    //请求的url
             CURLOPT_RETURNTRANSFER =>1,  //不要把请求的结果直接输出到浏览器
             CURLOPT_TIMEOUT =>30,        //请求超时设置
             CURLOPT_POST =>1,            //使用post请求此url
             CURLOPT_SSL_VERIFYPEER=>0,   //服务端不验证ssl证书
             CURLOPT_SSL_VERIFYHOST=>0,   //服务端不验证ssl证书
             CURLOPT_HTTPPROXYTUNNEL=>1,  //启用时会通过HTTP代理来传输
             CURLOPT_HTTPHEADER =>['content-type: application/json'],//请求头部设置
             CURLOPT_POSTFIELDS =>json_encode(['email'=>$this->email,'password'=>$this->password],JSON_UNESCAPED_UNICODE), //post请求时传递的参数
         ]);
         $content = curl_exec($ch);  //执行
         $err = curl_error($ch);
         curl_close($ch);
         if($err){
             return $err;
         }
         return json_decode($content);
     }

     public function generateSign(){
         //调用获取token的函数
         $content=$this->getToken();
         if(isset($content->code) && $content->code==1000){
             $open_api_token= $content->open_api_auth_token;//获取token;
             $sign = sha1($this->email.'&'.$open_api_token.'&'.$this->time);// 生成签名
             //登陆成功后可以把以下几个参数保存到session中
             session_start();
             $_SESSION['udest_time']=$this->time;
             $_SESSION['udest_email']=$this->email;
             $_SESSION['udest_sign']=$sign;

             return $sign;
         }
         return false;
     }

    /**
     * 获取agent_api_token
     */
    public  function getAgent(){
        $content=$this->getToken();
        $ch=curl_init();
        curl_setopt_array($ch,[
            CURLOPT_URL =>"http://shenzhenudesk2018.udesk.cn/open_api_v1/get_agent_token",  //请求的url
            CURLOPT_RETURNTRANSFER =>1,  //不要把请求的结果直接输出到浏览器
            CURLOPT_TIMEOUT =>30,        //请求超时设置
            CURLOPT_POST =>1,            //使用post请求此url
            CURLOPT_SSL_VERIFYPEER=>0,   //服务端不验证ssl证书
            CURLOPT_SSL_VERIFYHOST=>0,   //服务端不验证ssl证书
            CURLOPT_HTTPPROXYTUNNEL=>1,  //启用时会通过HTTP代理来传输
            CURLOPT_HTTPHEADER =>['content-type:application/json','open_api_token:'.$content->open_api_auth_token],//请求头部设置
            CURLOPT_POSTFIELDS =>json_encode(['email'=>$this->email,'agent_email'=>'bklydt57948@chacuo.net','timestamp'=>$this->time,'sign'=>$this->generateSign()],JSON_UNESCAPED_UNICODE), //post请求时传递的参数
        ]);
        $content = curl_exec($ch);  //执行
        $err = curl_error($ch);
        curl_close($ch);
        if($err){
            return $err;
        }
        return json_decode($content);
    }

 }



$udesk= new Udesk(['email'=>'shenzhenudesk@126.com','password'=>'udesk123']);

var_dump($udesk->getAgent());
echo "<br>";
$sign=$udesk->generateSign();
echo "http://shenzhenudesk2018.udesk.cn/open_api_v1/customers?email=".$_SESSION['udest_email']."&amp;timestamp=".$_SESSION['udest_time']."&amp;sign=".$sign;  //用&amp;代替&不然报错











