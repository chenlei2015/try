<?php

/**
 * 主要采用 fputcsv 与 fgetcvs 函数实现
 * Class Cvs_helper
 */
class Cvs_helper
{

    public $redis;
    public $query;
    public $db;
    public $limit = 10000; //设置相对较大 导出速度越快

    public function __construct()
    {
        $this->ParentPid = posix_getpid();
        $this->redis = new Redis();
        $this->redis->connect('192.168.71.141',7001);
        $this->redis->auth('yis@2019._');
        $this->redis->select(9);
        $this->db = new mysqli('192.168.71.141','devuser','yb123456','yb_tms_logistics');
    }

    /**
     * 导出
     * 80万数据15秒
     * @param bool $download
     * $download = true 时 浏览器直接下载打开
     * @return array
     */
    public function export($download=false){
        //切分数据
        if(!$this->redis->exists("export_queue")){
            if(!$this->splitData('export_queue',$this->limit)) return [];
        }
        //分段取出
        $export_fields = [
            "task_id"           => "任务ID",
            "serial_no"         => "费用流水号",
            "business_document_no" => "业务单号",
            "seller_no"         => "订单号",
            "tracking_no"       => "跟踪号",
            "company_code"      => "物流公司code",
            "business_type"     => "业务类型名称",
            "line_type"         => "业务类型",
            "fee_type"          => "业务类型名称",
            "weight"            => "实重",
            "charge_weight"     => "计费重",
            "weight_unit"       => "计费重单位",
            "volume"            => "体积",
            "charge_volume"     => "计费体积",
            "volume_unit"       => "体积单位",
            "charge_name"       => "费用名称",
            "source_amt"        => "原币值",
            "source_currency"   => "原币种",
            "acct_amt"          => "目标币值",
            "acct_currency"     => "目标币种",
            "exchange_rate"     => "汇率",
            "acct_date"         => "记账时间",
            "exchange_date"     => "交易时间",
            "trans_type"        => "运输类型",
            "ana_status"        => "分析状态",
            "create_time"       => "创建时间",
            "modify_time"       => "更新时间",
        ];

        $fields = array_keys($export_fields);
        $fileName = "tester_".time().".csv";
        if($download){
            header('Content-Type: application/vnd.ms-execl');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            //打开php标准输出流 以追加写入的方式打开
            $fp = fopen('php://output', 'a');
        }else{
            $fp = fopen($fileName, 'w');
        }
        //header 标题
        fputcsv($fp, array_values($export_fields));
        //数据
        while (($start_id = $this->sampling("export_queue")) && $start_id !==true ){
            $sql    = "select * FROM yibai_logistics_wyt_hwc_ship_cost_copy1 where company_code='WYT' and id >={$start_id} limit {$this->limit}";
            $result = $this->query($sql)->result_array($fields,true);

            foreach ($result as $item){
                fputcsv($fp, $item);
            }

            if($download){
                //刷新缓冲区
                ob_flush();
                flush();
            }

            unset($result);
        }

        fclose($fp);
    }

    /**
     * 导入
     * 80万数据70秒
     */
    public function import()
    {
        if (($handle = fopen("test_1605665888.csv", "r")) !== FALSE) {
            //读取标题
            $start_time = time();
            $header = fgetcsv($handle, 1500, ",");
            $export_fields = [
                "task_id"           => "任务ID",
                "serial_no"         => "费用流水号",
                "business_document_no" => "业务单号",
                "seller_no"         => "订单号",
                "tracking_no"       => "跟踪号",
                "company_code"      => "物流公司code",
                "business_type"     => "业务类型名称",
                "line_type"         => "业务类型",
                "fee_type"          => "业务类型名称",
                "weight"            => "实重",
                "charge_weight"     => "计费重",
                "weight_unit"       => "计费重单位",
                "volume"            => "体积",
                "charge_volume"     => "计费体积",
                "volume_unit"       => "体积单位",
                "charge_name"       => "费用名称",
                "source_amt"        => "原币值",
                "source_currency"   => "原币种",
                "acct_amt"          => "目标币值",
                "acct_currency"     => "目标币种",
                "exchange_rate"     => "汇率",
                "acct_date"         => "记账时间",
                "exchange_date"     => "交易时间",
                "trans_type"        => "运输类型",
                "ana_status"        => "分析状态",
                "create_time"       => "创建时间",
                "modify_time"       => "更新时间",
            ];
            $fields = implode(',',array_keys($export_fields));
            $values ='';
            $i = 0;
            //逐行读取
            while (($rows = fgetcsv($handle, 1500, ",")) !== FALSE) {
                foreach ($rows as $k => &$v){
                    if(in_array($k,[15])){
                        //乱码
                        $v = iconv('gbk','utf-8',$rows[15]);
                    }else{
                        $v = $rows[$k];
                    }
                }

                if(($i%10000) == 0){
                    $values = ltrim($values,',');
                    if(!empty($values)){
                          $sql = "insert into yibai_logistics_wyt_hwc_ship_cost_copy1 ({$fields}) values {$values};";
                          $this->db->query($sql);
                          file_put_contents('yy.txt',$sql.PHP_EOL,FILE_APPEND);
                    }
                    $values ='';
                    $values .= ",('".implode("','",$rows)."')";
                }else{
                      $values .= ",('".implode("','",$rows)."')";
                }
                $i++;
            }

            if($values !=''){
                $values = ltrim($values,',');
                $sql = "insert into yibai_logistics_wyt_hwc_ship_cost_copy1 ({$fields}) values {$values};";
                file_put_contents('yy.txt',$sql.PHP_EOL,FILE_APPEND);
                $this->db->query($sql);
            }

            $end_time = time();
            echo "总行数 ",$i, "\n";
            echo "总耗时", ($end_time - $start_time), "秒\n";
            echo "峰值内存", round(memory_get_peak_usage()/1000), "KB\n";
            fclose($handle);
        }
    }



    /**
     * 切分数据 进程从对列里拿去数据 进行处理
     */
    public function splitData($tail_queue,$limit)
    {
        $this->getDB()->query("set @ind =0");
        $this->getDB()->query("set @index =-1;");
        $sql = "select * FROM (select @ind := @ind + 1 as rid,id FROM yibai_logistics_wyt_hwc_ship_cost_copy1 where company_code='WYT') st where ((@INDEX := @INDEX + 1) > -1) and (@INDEX % {$limit} = 0);";
        $result = $this->query($sql)->result_array();
        if(!empty($result)){
            foreach ($result as $item){
                $this->redis->lPush($tail_queue,$item['id']);
            }
            return true;
        }else{
            return false;
        }
    }



    public function query($sql){
        $this->query = $this->getDB()->query($sql);
        return $this;
    }


    /**
     * @param string $fields
     * @param bool $export
     * @return array
     */
    public function result_array($fields = "*",$export = false){
        $result_array = [];
        while($row = $this->query->fetch_assoc()){
            if($export){
                if(is_array($fields)){
                    $temp = [];
                    foreach ($fields as $field){
                        array_push($temp,$row[$field]);
                    }
                    $result_array[] = $temp;
                }
            }else{
                $result_array[] = $row;
            }
        }
        return empty($result_array)?[]:$result_array;
    }

    /**
     *
     * @param $lock_key
     * @param $pool_key
     * @return bool
     */
    protected function sampling($queue)
    {
        $id =  $this->redis->rpop($queue);
        if (!$id) {
            return true;
        }else{
            return $id;
        }
    }

    /**
     * @return mysqli
     */
    public function getDB(){
        return $this->db;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->db->close();
    }

}

// 应用
//$export = new Cvs_helper();
//$export->import();
//$export->export(true);
