<?php
function debug($result){
   //报错日志开启
    ini_set("display_errors","1");//开启报错日志
    error_reporting(-1);        //所有报错

    error_reporting(E_ERROR);  //所有致命错误
    error_reporting(E_ALL ^ (E_NOTICE | E_STRICT | E_WARNING | E_DEPRECATED));//除了通知、严格模式、警告

    //临时扩大内存  /延长请求时间
    ignore_user_abort(true);  // 浏览器关闭的情况下 可以继续运行PHP
    set_time_limit(0);     //永不超时

    ini_set("memory_limit", "1024M");
    set_time_limit(3600);  // 超市时间为3600秒

    //获取执行当前脚的进程PID 这两个函数都可以
    $pid = getmypid();
    $pid = posix_getpid(); // 这个函数要安装php的扩展posix



    //在PHP中，可以使用memory_get_usage()获取当前分配给你的PHP脚本的内存量，单位是字节；使用memory_get_peak_usage()获取分配给你的PHP脚本的内存峰值字节数。
    //PHP中的选项memory_limit，指定了脚本允许申请的最大内存量，单位是字节。如果没有限制，将这个值设置为-1。
    echo "初始: ".memory_get_usage()."B\n";
    $str = str_repeat('hello', 1000);
    echo "使用: ".memory_get_usage()."B\n";
    unset($str);
    echo "释放: ".memory_get_usage()."B\n";
    echo "峰值: ".memory_get_peak_usage()."B\n";


    //php 命令行开启一个后台守护进程
    $session_uid = '788888';
    $path_entry = FCPATH.'index.php';
    //(
    //  第一种方式
    //  可以把/dev/null 可以看作"黑洞". 它等价于一个只写文件. 所有写入它的内容都会永远丢失. 而尝试从它那儿读取内容则什么也读不到.
    //  /dev/null 2>&1则表示吧标准输出和错误输出都放到这个“黑洞”，表示什么也不输出
    //)
    $cmd = sprintf('/usr/bin/php %s inland PR rebuild_pr %s > /dev/null 2>&1 &', $path_entry, $session_uid);

    //(
    //  第二种方式  记录日志到php.log
    //  nohup命令用于不挂断地运行命令（关闭当前session不会中断改程序，只能通过kill等命令删除）。
    //  使用nohup命令提交作业，如果使用nohup命令提交作业，那么在缺省情况下该作业的所有输出都被重定向到一个名为nohup.out的文件中，除非另外指定了输出文件。
    //  2>&1就是用来将标准错误2重定向到标准输出1中的。此处1前面的&就是为了让bash将1解释成标准输出而不是文件1。至于最后一个&，则是让bash在后台执行。
    //)
    $cmd = sprintf('nohup /usr/bin/php %s inland PR rebuild_pr %s >> php.log 2>&1 &', $path_entry, $session_uid);


    //第三种
    $path_entry = '/home/wwwroot/tms/appdal/index.php';
    $cmd = sprintf('/usr/bin/php %s ordersys/console/ShipCost/getCost %s %s > /dev/null 2>&1 &', $path_entry, 'WYT_model','jhkhkjh');

    // php 接受命令行形式传递的参数
    // func_num_args()  命令行形式传递的参数个数
    // func_get_args()  命令行形式传递的参数数组
    // if(is_cli() && (func_num_args() >0)) { $params = func_get_args()}
    shell_exec($cmd);



    //文件写入调试
    $data = [];
    $inputs = [];
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt','11'.PHP_EOL);//清空原来的内容 写入现在的内容
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt','11'.PHP_EOL,FILE_APPEND);//文件内容后追加
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt',json_encode($result).PHP_EOL,FILE_APPEND);
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt',"<?php\n".'return '.var_export($data, true).";\n\n?>",FILE_APPEND);
    file_put_contents(APPPATH.'/cache/100.php',"<?php\n".'return '.var_export(['11'=>$inputs], true).";\n\n?>",FILE_APPEND);
    file_put_contents(APPPATH.'/cache/100.php',"<?php\n".'return '.var_export($this->db->total_query(), true).";\n\n?>",FILE_APPEND);
    file_put_contents(APPPATH.'/cache/100.php','11'.PHP_EOL.PHP_EOL,FILE_APPEND);
}


//各进制数据转换
function  baseConvert(){
    echo bindec('110011'); // 二进制转十进制
    echo "<br>";

    echo octdec()('030');  // 八进制转十进制  八进制值被定义为带前置 0
    echo "<br>";

    echo hexdec()('0x56'); // 十六进制转十进制  十六进制值被定义为带前置 0x
    echo "<br>";

    echo base_convert();//各种进制相互转换
    echo "<br>";

    //chr() 函数从指定的 ASCII 值返回字符。
    //ASCII 值可被指定为十进制值、八进制值或十六进制值。八进制值被定义为带前置 0，而十六进制值被定义为带前置 0x。
    echo chr(61) . "<br>";   //  从十进制的ASCII值返回字符
    echo chr(061) . "<br>";  //  从八进制值的ASCII值返回字符
    echo chr(0x61) . "<br>"; //  从十六进制值的ASCII值返回字符

    //ord() 函数返回字符串第一个字符的ASCII值。是chr反向函数
    echo ord("h");
    echo ord("hello");
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
/**
 * 读取内容转化为字符串
 * @param string $file
 * @return array
 */


function rFile($file = "./log.txt" ,$before = "'",$after="',"){
    $handle = fopen($file,'r');
    $data = [];
    while(!feof($handle))
    {
        $row = fgets($handle);
        $str = str_replace(array("\r\n", "\r", "\n"), "", $row);
        $data[] = strval($str);
        file_put_contents('./test.log',$before.$str.$after.PHP_EOL,FILE_APPEND);
    }
    fclose($handle);
    return $data;
}

/**
 * 获取当前进程数量
 * @return int
 */
function get_process_num(){
    $check_process = shell_exec("ps -ef | grep 'fba PR rebuild_pr' | grep -v grep | awk '{print $2}' ");
    if (!is_null($check_process)) {
        $running_pids = array_filter(explode("\n", $check_process));
        if (!empty($running_pids)) {
            return count($running_pids);
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

/**
 * 更新与新增的数据分组
 * @param $passData 此数组包含更新与新增的数据
 * @param $model    此为表的实体对象
 */
public function groupUpdateInsertData($model,$passData)
{
    //根据 $passData 到数据库中查询 已处在的数据  inbound_number在数据表中可以确定唯一一条数据
    $condition = ['where_in' => ['inbound_number' => array_column($passData, 'inbound_number')]];
    $exit_data = $model->getDataByCondition($condition);

    //获取要更新的数据的唯一值条件集合
    $exit_data_gid = array_column($exit_data, null, 'inbound_number');
    $passData_gid = array_column($passData, null, 'inbound_number');
    $update_data_gid = array_intersect_key($passData_gid, $exit_data_gid);//唯一值条件集合

    //获取要新增的数据
    $insert_data = array_values(array_diff_key($passData_gid, $exit_data_gid));
}

/**
 * 调试性能
 */
function performance()
{
    $s = time();
    pr('初始内存：'.memory_get_usage());

    //todo :主体逻辑

    $e = time();
    pr('耗时：'.($e-$s));
    pr($this->db->total_query());
    pr('结束内存：'.memory_get_usage());
    pr('内存峰值：'.memory_get_peak_usage());
    die;

}

/**
 * 获取微妙数
 * @return float|int
 */
function getMicroTime(){
    $time = explode(' ',microtime());
    return ($time[1] + $time[0])*1000000;
}


//function grid($price){
//    $grid_array = [];
//    for ($i=0;$i<=99;$i++){
//        $pow_base = 1-1.1/100;
//        $pow = pow($pow_base,$i);
//        $grid_array[$i] = round($price*$pow,3);
//    }
//    return $grid_array;
//}
//
//
//$grid = grid(7);
//print_r($grid);
//
//echo array_sum($grid)/count($grid);
