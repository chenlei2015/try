<?php
/**
 * Created by PhpStorm.
 * User: Yibai
 * Date: 2019/7/31
 * Time: 18:51
 */

class Curl
{
    /**
     * 此方法依赖php的扩展模块pecl_http
     * 单进程
     * @param $url
     * @return mixed|string
     */
    function requestCurl($url,$header,$data_json){
        $request = new HttpRequest();
        $request->setUrl($url);
        $request->setMethod(HTTP_METH_POST);
        $request->setHeaders($header);
        $request->setBody($data_json);
//        $request->setHeaders(array(
//            'Postman-Token' => '01dead15-648a-4b0e-828c-84bb2a806f57',
//            'cache-control' => 'no-cache',
//            'Content-Type' => 'application/json'
//        ));
//        $request->setBody('{
//             "email": "admin@email.com",
//             "password": "xxxxpassword"
//        }');
        $response = $request->send();
        return $content = $response->getBody();
    }
    /**
     * 单进程
     * @param $url
     * @return mixed|string
     */
    function requestByCurlPost($url){
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
        return json_decode($content,true);
    }


    function requestByCurlGet($url){
        $ch = curl_init();
        curl_setopt_array($ch,[
            CURLOPT_URL =>$url,    //请求的url
            CURLOPT_RETURNTRANSFER =>1,  //不要把请求的结果直接输出到屏幕上
            CURLOPT_CUSTOMREQUEST=>'GET',
            CURLOPT_TIMEOUT =>30,        //请求超时设置
            CURLOPT_SSL_VERIFYPEER=>0,   //服务端不验证ssl证书
            CURLOPT_SSL_VERIFYHOST=>0,   //服务端不验证ssl证书
            CURLOPT_HTTPPROXYTUNNEL=>1,  //启用时会通过HTTP代理来传输
            //CURLOPT_HTTPHEADER =>['Content-type:text/html;charset=utf-8'],//请求头部设置
        ]);
        $content = curl_exec($ch);  //执行
        $err = curl_error($ch);
        curl_close($ch);
        if($err){
            return $err;
        }
        return $content;
    }


    /*
    * 描述: 多进程处理
    * 作者: wujianming
    */
    public  function getMultiProcess($urls = array()) {
        $handles = $contents = array();

        //初始化curl multi对象
        $mh = curl_multi_init();

        //添加curl 批处理会话
        foreach($urls as $key => $url) {
            $handles[$key] = curl_init($url);
            //不输出头
            curl_setopt($handles[$key], CURLOPT_HEADER, 0);
            //exec返回结果而不是输出,用于赋值
            curl_setopt($handles[$key], CURLOPT_RETURNTRANSFER, 1);
            //            curl_setopt($handles[$key], CURLOPT_TIMEOUT, 10);
            //决定exec输出顺序
            curl_multi_add_handle($mh, $handles[$key]);
        }

        //======================执行批处理句柄=================================
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active and $mrc == CURLM_OK) {
            if(curl_multi_select($mh) === -1){
                usleep(100);
            }

            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
        //====================================================================

        //获取批处理内容
        foreach($handles as $i => $ch) {
            $content = curl_multi_getcontent($ch);
            $contents[$i] = curl_errno($ch) == 0 ? $content : '';
        }

        //移除批处理句柄
        foreach($handles as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }

        //关闭批处理句柄
        curl_multi_close($mh);
        return $contents;
    }

}