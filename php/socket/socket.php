<?php
class Socket{

    public  function runThreadSOCKET($urls, $hostname = '', $port = 80) {
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

}