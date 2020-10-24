<?php


class PlatformShelfOutGc extends MY_ApiBaseController
{

    private $_modelObj;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_sys/PlatformShelfOutGc_model');
        $this->_modelObj = $this->PlatformShelfOutGc_model;
    }




    /**
     *
     * 导入
     * www.tms-f.com/api/platformShelfOutGc/import
     */
    public function import(){
        $this->load->library('journal_helper');
        $subdir = "journal/platform_shelf_out_gc/import/".date("Ym", time()).'/';
        list($status, $data) = $this->journal_helper->doUpload($subdir);
        if (!$status) {
            $this->_code = $this->getServerErrorCode();
            $this->_msg = $this->lang->line('upload_error') . $data;
            $this->sendData();
        }
        $params = ["file_path"=>$data['file_name']];
        list($status,$msg,$result) = $this->_modelObj->import($params);
        if (!$status) {
            $this->_code = $this->getServerErrorCode();
            $this->_msg = $msg;
        }
        $this->sendData($result);
    }



    /**
     *  下载导入失败列表
     *  http://www.tms-f.com/api/platformShelfOutGc/download
     */
    public  function download(){
        $params = gp();
        $file_path = $params['file_path'];
        $this->load->library('journal_helper');
        $filename  = '平台仓换标登记表之FB4上架出库导入失败列表.csv';
        if(!file_exists($file_path)){
            $file_fail_path = APPPATH.'/upload/template/download_file_not_find.csv';
            $this->journal_helper->download($file_fail_path,$filename);
        }else{
            $this->journal_helper->download($file_path,$filename);
        }
    }


    /**
     * 列表
     * www.tms-f.com/api/platformShelfOutGc/index
     */
    public function index()
    {
        $params = $this->_requestParams;

        // 一起返回下拉数据
        $data = $this->_modelObj->getList($params);

        if (is_null($data))
        {
            $this->_code = $this->getServerErrorCode();
            $this->_msg = $this->_modelObj->getErrorMsg();
        }

        $this->sendData($data);
    }


    /**
     * 新增
     * www.tms-f.com/api/platformShelfOutGc/addOne
     */
    public function addOne()
    {
        $params = $this->_requestParams;

        $params['add_data'] = '[
    {
        "warehouse_name": "出口易英国仓",
        "sku": "JM9999_01",
        "amount": "12",
        "seller_name": "张珊11",
        "account_name": "IN_ACCOUT_01",
        "new_tag": "儿童",
        "platform_order_id": "407-4307883-8197155",
        "gc_no": "gc_8888999666",
        "tracking_number": "WELED9345000625YQ",
        "ship_address": "dsklf kkkkk ljkj dslfdslfj",
        "far_fee": "0.560",
        "ship_fee": "0.56",
        "fba_change_fee": "6.00",
        "operate_fee": "0.53",
        "operate_piece_fee": "2.13",
        "operate_weight_fee": "2.40",
        "fuel_fee": "1.51",
        "mix_fuel_fee": "0.20",
        "total_fee": "13.89",
        "inbound_time": "2020-05-30",
        "exchange_rate": "6.5000",
        "rmb_ship_fee": "39.00",
        "surcharge": "0.00",
        "rmb_total_fee": "129.29",
        "handle_time": "2020-05-30"
    },
    {
        "warehouse_name": "出口易英国仓",
        "sku": "JM9999_02",
        "amount": "10",
        "seller_name": "张珊",
        "account_name": "IN_ACCOUT_01",
        "new_tag": "儿童",
        "platform_order_id": "407-4307883-8197155",
        "gc_no": "gc_8888999666",
        "tracking_number": "WELED9345000625YQ",
        "ship_address": "dsklf kkkkk ljkj dslfdslfj",
        "far_fee": "0.560",
        "ship_fee": "0.56",
        "fba_change_fee": "6.00",
        "operate_fee": "0.53",
        "operate_piece_fee": "2.13",
        "operate_weight_fee": "2.40",
        "fuel_fee": "1.51",
        "mix_fuel_fee": "0.20",
        "total_fee": "13.89",
        "inbound_time": "2020-05-30",
        "exchange_rate": "6.5000",
        "rmb_ship_fee": "39.00",
        "surcharge": "0.00",
        "rmb_total_fee": "129.29",
        "handle_time": "2020-05-30"
    },
    {
        "warehouse_name": "出口易英国仓",
        "sku": "JM9999_03",
        "amount": "10",
        "seller_name": "张珊",
        "account_name": "IN_ACCOUT_01",
        "new_tag": "儿童",
        "platform_order_id": "407-4307883-8197155",
        "gc_no": "gc_8888999666",
        "tracking_number": "WELED9345000625YQ",
        "ship_address": "dsklf kkkkk ljkj dslfdslfj",
        "far_fee": "0.560",
        "ship_fee": "0.56",
        "fba_change_fee": "6.00",
        "operate_fee": "0.53",
        "operate_piece_fee": "2.13",
        "operate_weight_fee": "2.40",
        "fuel_fee": "1.51",
        "mix_fuel_fee": "0.20",
        "total_fee": "13.89",
        "inbound_time": "2020-05-30",
        "exchange_rate": "6.5000",
        "rmb_ship_fee": "39.00",
        "surcharge": "0.00",
        "rmb_total_fee": "129.29",
        "handle_time": "2020-05-30"
    }
]';
        list($status, $msg) = $this->_modelObj->addOne($params);
        if (!$status)
        {
            $this->_code = $this->getServerErrorCode();
        }
        $this->_msg  = $msg;

        $this->sendData();
    }


    /**
     * 编辑
     * www.tms-f.com/api/platformShelfOutGc/editOne
     */
    public function editOne()
    {
        $params = $this->_requestParams;

        $params['id'] =48;
        $params['edit_data'] ='{
        "warehouse_name": "出口易英国仓",
        "sku": "JM9999_01",
        "amount": "10",
        "seller_name": "张珊",
        "account_name": "IN_ACCOUT_01",
        "new_tag": "儿童",
        "platform_order_id": "407-4307883-8197155",
        "gc_no": "gc_8888999666",
        "tracking_number": "WELED9345000625YQ",
        "ship_address": "dsklf kkkkk ljkj dslfdslfj",
        "far_fee": "0.560",
        "ship_fee": "0.56",
        "fba_change_fee": "6.00",
        "operate_fee": "0.53",
        "operate_piece_fee": "2.13",
        "operate_weight_fee": "2.40",
        "fuel_fee": "1.51",
        "mix_fuel_fee": "0.20",
        "total_fee": "13.89",
        "inbound_time": "2020-05-30",
        "exchange_rate": "6.5000",
        "rmb_ship_fee": "39.00",
        "surcharge": "0.00",
        "rmb_total_fee": "129.29",
        "handle_time": "2020-05-30"
    }';

        list($status, $msg) = $this->_modelObj->editOne($params);

        if (!$status)
        {
            $this->_code = $this->getServerErrorCode();
        }
        $this->_msg  = $msg;

        $this->sendData();
    }


    /**
     * 删除记录（支持批量删除）
     * www.tms-f.com/api/platformShelfOutGc/drop
     */
    public function drop()
    {
        $params = $this->_requestParams;
        //$params['gc_no'] = 'gc_8888999444,gc_8888999333';
        list($status, $msg) = $this->_modelObj->drop($params);

        if (!$status)
        {
            $this->_code = $this->getServerErrorCode();
        }
        $this->_msg  = $msg;

        $this->sendData();
    }


    /**
     * 操作日志
     * www.tms-f.com/api/platformShelfOutGc/actionLog
     */
    public function actionLog()
    {
        $params = $this->_requestParams;
        $data   = $this->PlatformShelfOutGc_model->getActionLog($params);
        $this->sendData($data);
    }


    /**
     * 创建导出任务
     * http://www.tms-f.com/api/platformShelfOutGc/export
     */
    public function export()
    {
        // 支持GET参数，方便测试
        $params = array_merge($this->input->get(), $this->_requestParams);
        $params['gc_no'] = "gc_8888999333,gc_8888999444,gc_8888999666";
        list($status, $msg) = $this->_modelObj->export($params);
        if (!$status)
        {
            $this->_code = $this->getServerErrorCode();
        }
        $this->_msg  = $msg;

        $this->sendData();
    }


    /**
     * 下载导入模板（前端需要传入参数指明 渠道业务类型 下载相应的模板）
     * http://www.tms-f.com/api/platformShelfOutGc/downloadTpl
     */
    public function downloadTpl()
    {
        $params = $this->_requestParams;
        $params['page_size'] = 1;
        $result = $this->_modelObj->getList($params);

        $titleTpl   = array_keys($result['data_list']['drop_down_box']['template_title']);
        $filedTitle = array_flip($result['data_list']['drop_down_box']['template_title']);

        $this->load->helper('Excel');
        $excelHelper = new Excel_helper('write_row');
        $excelHelper->writeRow(1, 1, $titleTpl);
        $lastRow = chr(65 + count($titleTpl) - 1);
        $excelHelper->getExcelObj()->getActiveSheet()->getStyle('A1:' . ($lastRow . '1'))->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '008000') // 绿色
                )
            )
        );
        $data   = $result['data_list']['value'];
        $row = 2;
        foreach ($data as $k => $item)
        {
            array_walk($filedTitle,function (&$v,$k,$item){
                $v = $item[$k];
            },$item);
            $row = $row +$k;
            $col = $k+1;
            $excelHelper->writeRow($row, $col, $filedTitle);
        }
        $excelHelper->download("物流轨迹拉取配置导入模板.xlsx");
    }

}