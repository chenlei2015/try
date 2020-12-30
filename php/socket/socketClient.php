<?php
class socketClient{
    /**
     * 主要是利用socket的客户端函数 fsockopen 向服务端发出批量请求
     * @param $urls
     * @param string $hostname
     * @param int $port
     */
    public  function runThreadSocketBatch($urls, $hostname = '', $port = 80) {
        if (!$hostname) {
            $hostname = $_SERVER['HTTP_HOST'];
            $port = $_SERVER['SERVER_PORT'] ?: $port;
        }

        if (!is_array($urls)){
            $urls = (array)$urls;
        }
        foreach ($urls as $url) {
            $fp=fsockopen($hostname, $port, $errno, $errstr,18000);
            stream_set_blocking ( $fp, true );
            stream_set_timeout ( $fp, 18000 );
            fputs($fp,"GET ".$url." HTTP/1.1\r\n");
            fputs($fp,"Host: ".$hostname."\r\n\r\n");
            fclose($fp);
        }
    }

    /**
     * 描述:模拟多线程
     * @param     $urls
     * @param int $timeOut
     *   $url = 'http://tmsservice.yibainetwork.com:92/ordersys/api/FailOrder/getTrackNumber?platformCode=WISH&order=wh789451234582';
     */
    public static function runThreadSocket($urls, $timeOut = 1800)
    {
        if (!is_array($urls)) {
            $urls = (array)$urls;
        }
        foreach ($urls as $url) {
            $info     = parse_url($url);
            $hostname = $info['host']; //主机
            $port     = isset($info['port']) ? $info['port'] : 80;  //端口号
            $path     = isset($info['query']) ? $info['path'] . "?" . $info['query'] : $info['path']; //相对路径与请求参数
            $fp       = fsockopen($hostname, $port, $errno, $errstr, $timeOut);
            stream_set_blocking($fp, true);
            stream_set_timeout($fp, $timeOut);
            fputs($fp, "GET " . $path . " HTTP/1.1\r\n");
            fputs($fp, "Host: " . $hostname . "\r\n\r\n");
            fclose($fp);
        }
    }

}