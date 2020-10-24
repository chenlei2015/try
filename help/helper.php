<?php
function debug($result){
   //报错日志开启
    ini_set("display_errors","1");//开启报错日志
    error_reporting(-1);        //所有报错

    error_reporting(E_ERROR);  //所有致命错误
    error_reporting(E_ALL ^ (E_NOTICE | E_STRICT | E_WARNING | E_DEPRECATED));//除了通知、严格模式、警告

    //临时扩大内存  /延长请求时间
    ignore_user_abort(true);  // 浏览器关闭的情况下 可以继续运行PHP
    ini_set("memory_limit", "1024M");
    set_time_limit(3600);  // 超市时间为3600秒
    set_time_limit(0);     //永不超时

    //获取当前进程ID
    $pid = getmypid();
    $data = [];
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt','11'.PHP_EOL);//清空原来的内容 写入现在的内容
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt','11'.PHP_EOL,FILE_APPEND);//文件内容后追加
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt',json_encode($result).PHP_EOL,FILE_APPEND);
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt',"<?php\n".'return '.var_export($data, true).";\n\n?>",FILE_APPEND);
    file_put_contents(APPPATH.'/cache/100.php',"<?php\n".'return '.var_export(['11'=>$inputs], true).";\n\n?>",FILE_APPEND);
    file_put_contents(APPPATH.'/cache/100.php',"<?php\n".'return '.var_export($this->db->total_query(), true).";\n\n?>",FILE_APPEND);
    file_put_contents(APPPATH.'/cache/100.php','11'.PHP_EOL,FILE_APPEND);
}


//调试打印
if (!function_exists('pr')) {
    function pr($arr, $escape_html = true, $bg_color = '#EEEEE0', $txt_color = '#000000')
    {
        echo sprintf('<pre style="background-color: %s; color: %s;">', $bg_color, $txt_color);
        if ($arr) {
            if ($escape_html) {
                echo htmlspecialchars(print_r($arr, true));
            } else {
                print_r($arr);
            }

        } else {
            var_dump($arr);
        }
        echo '</pre>';
    }
}


function grid($price){
    $grid_array = [];
    for ($i=0;$i<=99;$i++){
        $pow_base = 1-1.1/100;
        $pow =pow($pow_base,$i);
        $grid_array[$i] = round($price*$pow,3);
    }
    return $grid_array;
}


$grid = grid(7);
print_r($grid);

echo array_sum($grid)/count($grid);
