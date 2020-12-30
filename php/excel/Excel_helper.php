<?php

/**
 * 文档：https://xlswriter-docs.viest.me/zh-cn
 * 需要装php 安装 扩展 xmlwriter
 * excel 表格数据导入 （读取）
 * excel 表格数据导出  （写入）
 * Class Excel_helper
 */
class Excel_helper {
    public $redis;
    public $query;
    public $db;
    public $limit = 10000; //设置相对较大 导出速度越快
    public $excel;

    public function __construct()
    {
        $this->ParentPid = posix_getpid();
        $this->redis = new Redis();
        $this->redis->connect('192.168.71.141',7001);
        $this->redis->auth('yis@2019._');
        $this->redis->select(9);
        $this->db = new mysqli('192.168.71.141','devuser','yb123456','yb_tms_logistics');
        $this->excel = new \Vtiful\Kernel\Excel(['path' =>'/home/wwwroot/try']);
    }

    /**
     * 导出
     * 所有数据导出到同一个工作表
     */
    public function export(){
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
        $header = array_values($export_fields);
        $fileName = "test_".time().".xlsx";
        $file = $this->excel->fileName($fileName)->header($header);
        while (($start_id = $this->sampling("export_queue")) && $start_id !==true ){
            $sql    = "select * FROM yibai_logistics_wyt_hwc_ship_cost_copy1 where company_code='WYT' and id >={$start_id} limit {$this->limit}";
            $result = $this->query($sql)->result_array($fields,true);
            if(!empty($result)){
                $file->data($result)->output();
                unset($result);
            }
        }
    }


    /**
     * 导出
     * 数据导出到不同工作表
     * @return array
     */
    public function exporter(){
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
        $header = array_values($export_fields);
        $fileName = "test_".time().".xlsx";
        $file = $this->excel->fileName($fileName);
        $count = 0;
        while (($start_id = $this->sampling("export_queue")) && $start_id !==true ){
            $sql    = "select * FROM yibai_logistics_wyt_hwc_ship_cost_copy1 where company_code='WYT' and id >={$start_id} limit {$this->limit}";
            $result = $this->query($sql)->result_array($fields,true);
            if(!empty($result)){
                //每10000条数据写入一个工作表
                if((($count*$this->limit)%10000) == 0){
                    file_put_contents('/home/wwwroot/try/lockrty.txt',"page start fpid: ".$count.PHP_EOL.PHP_EOL,FILE_APPEND);
                    if($count===0){
                        //sheet1工作表
                        $file->header($header);
                    }else{
                        //sheet2、sheet3、sheet4 等工作表
                        $file->addSheet()->header($header);
                    }
                }
                $file->data($result)->output();
                unset($result);
            }
            $count++;
        }
    }


    /**
     * 全量读取(导入)
     */
    public function read_all(){
       //全量读取  tutorial.xlsx test_1604498978.xlsx
        $data = $this->excel->openFile('test_1604498978.xlsx')->openSheet("Sheet1")
            //->setSkipRow(1) //忽略第一行
            ->getSheetData();
            var_dump($data);
    }

    /**
     * 使用该种方法 导入80万数据到数据库,只需90秒
     * 逐行读取（导入）
     */
    public function read_line(){
        $start_time = time();
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
        //逐行读取  tutorial.xlsx test_1604498978.xlsx
        $excel = $this->excel->openFile('test_1605665880.xlsx')->openSheet("Sheet1");
        //读取标题头
        $header = $excel->nextRow();
        $values ='';
        $i = 0;
        //读取数据部分
        while(($rows = $excel->nextRow()) !== null){
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
            $this->db->query($sql);
        }

        $end_time = time();
        echo "总行数 ",$i, "\n";
        echo "总耗时", ($end_time - $start_time), "秒\n";
        echo "峰值内存", round(memory_get_peak_usage()/1000), "KB\n";
    }

    /**
     *  利用 mysql 的 load data infile命令实现
     *  10万数据导入数据库中 可以说load data infile是秒级响应
     */
    public function read_file(){
        ini_set("display_errors","1");//开启报错日志
        error_reporting(-1);        //所有报错
        $export_fields = [
            "id"                => "ID",
            "task_id"           => "任务ID",
            "serial_no"         => "费用流水号",
            "business_document_no" =>"业务单号",
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

        // load data local infile  mysql服务器与 test_1605665888.csv在同一台主机
        //$sql =  "load data local infile '/home/wwwroot/try/test_1605665888.csv' into table yibai_logistics_wyt_hwc_ship_cost_copy1 fields terminated by',' enclosed by '\"' ignore 1 lines ({$fields});";

        // load data infile  mysql服务器与 test_1605665888.csv不在同一台主机
        $sql =  "load data infile '/home/wwwroot/try/test_1605665888.csv' into table yibai_logistics_wyt_hwc_ship_cost_copy1 fields terminated by',' enclosed by '\"' ignore 1 lines ({$fields});";
        $this->db->query($sql);
    }


    /**
     * 切分数据 进程从对列里拿去数据 进行处理
     */
    public function splitData($tail_queue,$limit)
    {
        $this->getDB()->query("set @ind =0");
        $this->getDB()->query("set @index =-1;");
        $sql = "select * FROM (select @ind := @ind + 1 as rid,id FROM yibai_logistics_wyt_hwc_ship_cost where company_code='WYT') st where ((@INDEX := @INDEX + 1) > -1) and (@INDEX % {$limit} = 0);";
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


    /**
     * 返回结果集
     * @param $sql
     * @return $this
     */
    public function query($sql)
    {
        $this->query = $this->getDB()->query($sql);
        return $this;
    }


    public function result_array($fields = "*",$export = false)
    {
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
    public function getDB()
    {
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


//应用
//$export = new Excel_helper();
//$export->read_file();
//$export->exporter();
//$export->import_line();


