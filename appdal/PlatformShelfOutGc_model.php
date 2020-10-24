<?php


class PlatformShelfOutGc_model extends MY_Model
{

    protected $table_name = 'yibai_platform_shelf_out_gc';
    protected $database = "default";

    // 自动填充 create_time, modify_time 字段
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'modify_time';

    // 自动填充 create_user, modify_user 字段
    protected $_hasUserField = true;

    //列表分页配置
    private $_defaultPageSize = DEFAULT_PAGE_SIZE;
    private $_maxPageSize     = MAX_PAGE_SIZE;


    /**
     * 导入
     * 报表下拉框翻译数据
     * @var array
     */

    public $journalBoxList = [];

    /**
     * 导入
     * 报表名称
     * @var array
     */
    public $journalName = "platform_shelf_out_gc";

    /**
     * 导入
     * PlatformShelfDestroy_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function table_nameName()
    {
        return $this->table_name;
    }



    /**
     * 导入
     * 导入模板的标题-字段名称对应关系 从上到下顺序 要与导入的Excel表从左到右的字段顺序一样
     * @return array
     */
    public function getTitleFieldMap()
    {
        return array(
            '处理时间'                         =>    'handle_time',
            '仓库'                             =>    'warehouse_name',
            'SKU'                              =>    'sku',
            '数量'                             =>    'amount',
            '销售'                             =>    'seller_name',
            '转入账号名'                       =>    'account_name',
            '新标签'                           =>    'new_tag',
            '平台单号'                         =>    'platform_order_id',
            '谷仓订单号'                       =>    'gc_no',
            '物流单号'                         =>    'tracking_number',
            '配送地址'                         =>    'ship_address',
            '偏远费'                           =>    'far_fee',
            '运费'                             =>    'ship_fee',
            'FBA换标费'                        =>    'fba_change_fee',
            '操作费'                           =>    'operate_fee',
            '操作费按件数'                     =>    'operate_piece_fee',
            '操作费按重量'                     =>    'operate_weight_fee',
            '燃油附加费'                       =>    'fuel_fee',
            '杂费燃油附加费'                   =>    'mix_fuel_fee',
            '总费用'                           =>    'total_fee',
            '出库时间'                         =>    'inbound_time',
            '汇率'                             =>    'exchange_rate',
            '运输费RMB'                        =>    'rmb_ship_fee',
            '附加费RMB'                        =>    'surcharge',
            '总费用RMB'                        =>    'rmb_total_fee'
        );
    }

    /**
     * 导入
     * 报表下拉框翻译数据
     * @var array
     */
//    public function JournalBoxList(){
//        $fieldList = ['warehouseList'];
//        $this->load->model('services/Metadata_model');
//        $dropdownBox = $this->Metadata_model->batchGetList($fieldList);
//        $drop_down_box_other = [];
//        return array_merge($dropdownBox,$drop_down_box_other);
//    }


    /**
     * 导入
     * @param $data
     * @return array
     */
    public function importData($data){
        // 第一步：获取下拉列表数据
        $failData       = []; //验证失败的数据
        $passData       = []; //验证通过的数据
        // 第二步：验证数据
        $validate_res = $this->validateData($data);
        if(empty($validate_res)){
            //转换成可以在数据库中直接保存或修改的数据
            $saveData = $this->getSaveData($data);
            file_put_contents(APPPATH.'/cache/33.php',"<?php\n".'return '.var_export($saveData, true).";\n\n?>",FILE_APPEND);
            //添加到验证成功数据组
            array_push($passData,$saveData);
        }else{
            //添加到验证失败数据组
            $data['errorMsg'] = implode(',',$validate_res[0]);
            array_push($failData,$data);
        }

        return ['passData'=>$passData,'failData'=>$failData];
    }


    /**
     * 导入
     * 批量把验证通过的数据分成更新和新增两组
     */
    public function groupUpdateInsertData($passData){
        $condition     =  ['where_in'=>['gid'=>array_column($passData,'gid')]];
        $exit_data     =  $this->getDataByCondition($condition);
        $passData_gid  =  array_column($passData,null,'gid');
        $exit_data_gid =  array_column($exit_data,null,'gid');
        $update_data_gid = array_intersect_key($passData_gid,$exit_data_gid);
        $insert_data   =  array_values(array_diff_key($passData_gid,$exit_data_gid));


        $db = $this->getDB();
        try{
            $db->trans_start();
            //更新
            if(!empty($update_data_gid)){
                //获取更新数据
                $update_data = $this->getUpdateData($exit_data_gid,$update_data_gid);

                //更新
                if(!empty($update_data)){
                    if(!$this->_db->update_batch($this->table_name, $update_data, 'gid')) throw new Exception("更新数据失败");
                }
            }

            //新增
            if(!empty($insert_data)){
                if(!$this->batchInsert($insert_data,true)) throw new Exception("新增数据失败");
            }
            $db->trans_complete();
            return array('status'=>1,'count'=>count($passData));
        }catch (Exception $e){
            $db->trans_rollback();
            return array('status'=>0,'count'=>count($passData));
        }
    }

    /**
     * 导入
     * @param $oldData
     * @param $newData
     * @return array
     */
    public function getUpdateData($oldData,$newData){
        $update_data = [];
        $key_map = array_flip($this->getTitleFieldMap());
        foreach ($oldData as $key => $old){
            if(isset($newData[$key])){
                $new              = $newData[$key];
                $log_content      = array_value_diff($old,$new,$key_map);
                if(!empty($log_content)) {
                    unset($new['create_time']);
                    unset($new['create_user']);
                    $update_data[] = $new;
                }
            }
        }
        return $update_data;
    }

    /**
     * 导入
     * 可在此方法进行数据转换 获取可以直接插入数据库中的值
     * @param $data
     */
    public function getSaveData($data){
        $data['gid'] = $this->getGid($data);
        $data['handle_time'] = date("Y-m-d",strtotime($data['handle_time']));
        $data['inbound_time'] = empty($data['inbound_time'])?"0000-00-00 00:00:00":date("Y-m-d",strtotime($data['inbound_time']));
        $data['create_user'] = $this->getUserIdentity();
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['modify_user'] = $this->getUserIdentity();
        $data['modify_time'] = date("Y-m-d H:i:s");
        return $data;
    }


    /**
     * 导入
     * 获取聚合ID
     * @param $data
     * @return string
     */
    public function getGid($data){
        return md5($data['gc_no'].$data['sku']);
    }


    /**
     * 导入
     * 校验字段
     * @param array $rowData
     * @return mixed
     */
    public function validateData($rowData)
    {
        $row_error = [];
        $this->load->library('form_validation');
        $this->form_validation->set_data($rowData);

        //验证handle_time
        $this->form_validation->set_rules(
            'handle_time',
            '处理时间',
            [
                'required',
                'regex_match[/^20\d{2}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/]',//2020-50-30 14:25:30 或 2020-50-30
            ],
            [
                'required' => '{field}不能为空',
                'regex_match' => '{field}格式不正确,格式为2020/05/23',
            ]
        );

        //验证warehouse_name
        $this->form_validation->set_rules(
            'warehouse_name',
            '仓库',
            [
                'required',
            ],
            [
                'required' => '{field}不能为空',
            ]
        );

        //验证sku
        $this->form_validation->set_rules(
            'sku',
            'SKU',
            [
                'required',
            ],
            [
                'required' => '{field}不能为空',
            ]
        );


        //验证amount
        $this->form_validation->set_rules(
            'amount',
            '数量',
            [
                'required',
                'regex_match[/^[1-9]\d*$/]',
            ],
            [
                'required' => '{field}不能为空',
                'regex_match' => '{field}必须为正整数',
            ]
        );


        //验证seller_name
        $this->form_validation->set_rules(
            'seller_name',
            '销售',
            [
                'required',
            ],
            [
                'required' => '{field}不能为空',
            ]
        );


        //验证account_name
        $this->form_validation->set_rules(
            'account_name',
            '转入账号名',
            [
                'required',
            ],
            [
                'required' => '{field}不能为空',
            ]
        );

        //验证new_tag
        $this->form_validation->set_rules(
            'new_tag',
            '新标签',
            [
                'required',
            ],
            [
                'required' => '{field}不能为空',
            ]
        );

        //验证platform_order_id
        $this->form_validation->set_rules(
            'platform_order_id',
            '平台单号',
            [
                'required',
            ],
            [
                'required' => '{field}不能为空',
            ]
        );

        //验证gc_no
        $this->form_validation->set_rules(
            'gc_no',
            '谷仓订单号',
            [
                'required',
            ],
            [
                'required' => '{field}不能为空',
            ]
        );

        if($this->form_validation->run() == FALSE) {
            array_push($row_error,$this->form_validation->error_array());
        }

        $this->form_validation->reset_validation();
        return $row_error;
    }



    /**
     * 分页获取配置列表
     * @param array $params
     * @return array
     */
    public function getByPage($params = array())
    {
        // 1. 搜索条件
        $condition = array();
        //处理时间
        if(isset($params["handle_time_start"]) && !empty($params["handle_time_start"])){
            $condition['handle_time >= '] = $params["handle_time_start"];
        }

        if(isset($params["handle_time_end"]) && !empty($params["handle_time_end"])){
            $condition['handle_time <= '] = $params["handle_time_end"];
        }

        //仓库
        if (isset($params["warehouse_name"]) && !empty($params["warehouse_name"]))
        {
            $condition['warehouse_name LIKE '] = '%'.$params["warehouse_name"] . '%';
        }


        // sku
        if(isset($params["sku"]) && !empty($params["sku"])){
            $sku = explode(',',$params["sku"]);
            if(count($sku) >1){
                $condition['where_in']['sku'] = $sku;
            }else{
                $condition['sku'] = trim($params["sku"]);
            }
        }


        // 销售
        if (isset($params["seller_name"]) && !empty($params["seller_name"]))
        {
            $sku = explode(',',$params["seller_name"]);
            if(count($sku) >1){
                $condition['where_in']['seller_name'] = $sku;
            }else{
                $condition['seller_name'] = trim($params["seller_name"]);
            }
        }

        //平台单号
        if(isset($params["platform_order_id"]) && !empty($params["platform_order_id"])){
            $order_no = explode(',',$params["platform_order_id"]);
            if(count($order_no) >1){
                $condition['where_in']['platform_order_id'] = $order_no;
            }else{
                $condition['platform_order_id'] = trim($params["platform_order_id"]);
            }
        }

        //谷仓订单号
        if(isset($params["gc_no"]) && !empty($params["gc_no"])){
            $order_no = explode(',',$params["gc_no"]);
            if(count($order_no) >1){
                $condition['where_in']['gc_no'] = $order_no;
            }else{
                $condition['gc_no'] = trim($params["gc_no"]);
            }
        }

        // 2. 排序
        $orderBy = 'create_time DESC';
        if (!empty($params['order_by_create_time']))
        {
            $orderBy = 'create_time ' .  ($params['order_by_create_time'] > 0 ? 'ASC' : 'DESC');
        }

        // 3. 分页
        $pageSize = !isset($params['page_size']) || intval($params['page_size']) <= 0 ?
            $this->_defaultPageSize :
            min(intval($params['page_size']), $this->_maxPageSize);
        $page = !isset($params['page']) || intval($params['page']) <= 0 ? 1 : intval($params['page']);
        $offset = ($page - 1) * $pageSize;

        // 获取数据
        $fields = '*';
        $result = $this->getDataList($condition, $fields, $orderBy, $offset, $pageSize);

        return array(
            'count' => $result['total'],
            'page_count' => ceil($result['total'] / $pageSize),
            'list' => $result['data'],
            'drop_down_box' => ['template_title'=> $this->getTitleFieldMap()] //加入下载模板的标题头参数
        );
    }


    /**
     * 配置是否已经存在
     * @param $ship_codes
     */
    public function isExistData($condition){
        $result = $this->findOne($condition);
        return $result;
    }


    /**
     * 添加一条记录
     * @param array $params
     * @return array
     */
    public function addOne(array $params)
    {
        $addNewData = [];
        $errorMsg = null;
        foreach ($params as $key => $param){
            //验证数据
            $validate_res = $this->validateData($param);
            if(!empty($validate_res)){
                $errorMsg = implode(',',$validate_res[0]);
                break;
            }

            //验证数据是否存在
            $gid = $this->getGid($param);
            $existData =  $this->isExistData(['gid'=>$gid]);
            if(!empty($existData)){
                $errorMsg = "平台单号为{$param['platform_order_id']}且sku为{$param['sku']}的数据已存在,新增失败";
                break;
            }

            //验证新政数据中sku的唯一性
            $insert_data = $this->getSaveData($param);
            if(!isset($addNewData[$gid])){
                $addNewData[$gid] = $this->filterNotExistFields($insert_data);
            }else{
                $errorMsg = "新增的数据中sku为{$param['sku']}的数据已存在,新增失败";
                break;
            }
        }


        //验证
        if(!empty($errorMsg)){
            return array(false,$errorMsg);
        }

        // 保存
        $data = array_values($addNewData);
        $result = $this->batchInsert($data,true);
        if (!$result)
        {
            return array(false, "添加失败:" . $this->getWriteDBError());
        }
        return array(true, '添加成功');
    }


    /**
     * 更新一条记录
     * @param int $id 要更新的记录id
     * @param array $params 更新的键值对
     * @return array
     */
    public function editOne($id, array $param)
    {
        $id = intval($id);

        if (empty($id)) {
            return array(false, "id 不能为空");
        }

        //验证数据是否存在
        $existData =  $this->isExistData(['id'=>$id,'gc_no'=>$param['gc_no'],'sku'=>$param['sku']]);
        if(empty($existData)){
            $errorMsg = "平台单号为{$param['platform_order_id']}且sku为{$param['sku']}的数据不已存在,编辑失败";
            return array(false, $errorMsg);
        }

        //组合编辑的数据
        $edit_data = $this->getSaveData($param);

        // 过滤非法字段
        $data = $this->filterNotExistFields($edit_data);
        if (empty($data)) {
            return array(false, "没有修改的数据");
        }

        //获取日志与编辑数据
        $oldData = [$existData['gid']=>$existData];
        $newData = [$data['gid']=>$data];
        $update_data = $this->getUpdateData($oldData,$newData);
        $db = $this->getDB();
        try{
            $db->trans_start();
            //更新
            if(!empty($update_data)){
                if(!$this->_db->update_batch($this->table_name, $update_data, 'gid')) throw new Exception("更新数据失败");
            }
            $db->trans_complete();
            return array(true, "更新成功");
        }catch (Exception $e){
            $db->trans_rollback();
            return array(false, "更新失败");
        }
    }

    /**
     * 删除 也可批量删除
     * @param array $order_noes 系统单号
     * @return array
     */
    public function batchDrop(array $dropParams){
        if (empty($dropParams)) {
            return array(false, "gc_no 不能为空");
        }
        $this->delete(['where_in'=>['gc_no'=>$dropParams]]);
        if ($this->getAffectedRows() == 0) {
            return array(false, "删除失败:" . $this->getWriteDBError());
        }
        return array(true, "删除成功");
    }


    /**
     * 创建导出任务
     * @param array $params
     * @return array
     */
    public function exportToTask($params = [])
    {
        $this->load->model('ordersys/logistics/CommonFiles_model');
        //导出批次
        $result = $this->getByPage($params);
        if (empty($result['count'])) {
            return array(false, "没有需要导出的数据");
        }

        if (empty($params)) {
            $params = "{}";
        } else {
            $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        }

        $status = $this->CommonFiles_model->addExportTask('/ordersys/api/platformShelfOutGc/exportToFile', '平台仓换标登记表之谷仓上架出库报表', $params, $result['count']);
        return array($status, $status ? "导出成功" : "导出失败");

    }

    /**
     * 执行导出任务
     */

    public function exportToFile(array $params)
    {
        $this->load->model('ordersys/customs/CommonExport_model');
        $exportModel = $this->CommonExport_model;
        //导出标题
        $exportModel->fieldTitleMap = array_keys($this->exportFieldTitleMap());
        $exportModel->createExportFild($params)->markExport()->startExport($this)->endExport();
        return array(true, $exportModel->fileName);
    }

    /**
     * 导出Excel标题头
     * @return array
     */
    public function exportFieldTitleMap()
    {
        return array(
            '处理时间'                         =>    'handle_time',
            '仓库'                             =>    'warehouse_name',
            'SKU'                              =>    'sku',
            '数量'                             =>    'amount',
            '销售'                             =>    'seller_name',
            '转入账号名'                       =>    'account_name',
            '新标签'                           =>    'new_tag',
            '平台单号'                         =>    'platform_order_id',
            '谷仓订单号'                       =>    'gc_no',
            '物流单号'                         =>    'tracking_number',
            '配送地址'                         =>    'ship_address',
            '偏远费'                           =>    'far_fee',
            '运费'                             =>    'ship_fee',
            'FBA换标费'                        =>    'fba_change_fee',
            '操作费'                           =>    'operate_fee',
            '操作费按件数'                     =>    'operate_piece_fee',
            '操作费按重量'                     =>    'operate_weight_fee',
            '燃油附加费'                       =>    'fuel_fee',
            '杂费燃油附加费'                   =>    'mix_fuel_fee',
            '总费用'                           =>    'total_fee',
            '出库时间'                         =>    'inbound_time',
            '汇率'                             =>    'exchange_rate',
            '运输费RMB'                        =>    'rmb_ship_fee',
            '附加费RMB'                        =>    'surcharge',
            '总费用RMB'                        =>    'rmb_total_fee',
            '创建人'                           =>    'create_user',
            '创建时间'                         =>    'create_time',
        );
    }

    /**
     * 导出
     * @param $records
     * @return mixed
     */
    public function  translate(&$records)
    {
        $exportTitleFieldMap = $this->exportFieldTitleMap();
        $exportFieldTitleMap = array_flip($exportTitleFieldMap);
        foreach ($records as $key=>$item){
            array_walk($exportFieldTitleMap,function (&$v,$k,$item){
                $v = $item[$k];
            },$item);
            $records[$key] = $exportFieldTitleMap;
        }
        return $records;
    }

}
