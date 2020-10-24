<?php


class PlatformShelfOutGc_model   extends Api_base_model
{
    const MODULE_NAME = 'ORDER_SYS'; // 模块名称

    protected $_baseUrl;    // 统一地址前缀
    protected $_importUrl;  // 导入
    protected $_listUrl;    // 列表
    protected $_addUrl;     // 添加
    protected $_editUrl;    // 编辑
    protected $_dropUrl;    // 删除
    protected $_logUrl;     // 日志
    protected $_exportUrl;  // 导出


    //页面头部字段
    protected $_tableHeader = array(
        '处理时间','仓库','SKU','数量','销售','转入账号名','新标签','货件编号（平台单号）','谷仓订单号','物流单号','配送地址','偏远费','运费','FBA换标费','操作费','操作费按件数','操作费按重量','燃油附加费','杂费燃油附加费','总费用','出库时间','汇率','运输费(RMB)','附加费(RMB)','总费用(RMB)','操作人员','操作时间','操作'
    );


    public function __construct()
    {
        parent::__construct();
        $this->init();
    }



    /**
     * 导入
     * 导入Excel
     */
    public function import(array $params) {
        $url    = $this->_baseUrl . $this->_importUrl;
        $result = $this->httpRequest($url, $params, 'POST');
        if (empty($result) || !isset($result['status']) || !isset($result['data']))
        {
            return [false, json_encode($result, JSON_UNESCAPED_UNICODE),null];
        }
        return [$result['status'],$result['message'],$result['data']];
    }


    /**
     * 创建导出配置任务
     * @param array $params
     * @return array
     */
    public function export(array $params)
    {
        $url    = $this->_baseUrl . $this->_exportUrl;

        $result = $this->httpRequest($url, $params, 'POST');
        if (empty($result) || !isset($result['status']))
        {
            return [false, json_encode($result, JSON_UNESCAPED_UNICODE)];
        }

        return [$result['status'], $result['message']];
    }



    /**
     * 列表页接口
     * @return array
     */
    public function getList($params = array())
    {
        // 1.预处理请求参数
        $params['page_size'] = !isset($params['page_size']) || intval($params['page_size']) <= 0 ?
            $this->_defaultPageSize : intval($params['page_size']);

        if (!isset($params['page']) || intval($params['page']) <= 0) {
            $params['page'] = 1;
        }

        // 2.调用接口
        $url = $this->_baseUrl . $this->_listUrl;
        $url .= '?' . http_build_query($params);

        $result = $this->httpRequest($url, '', 'GET');

        // 3.确认返回的数据是否与预期一样
        if (empty($result) || !isset($result['status']) || !isset($result['data'])) {
            $this->_errorMsg = json_encode($result);
            return null;
        }
        if (!empty($result['message'])) {
            $this->_errorMsg = $result['message'];
        }
        if (!$result['status']) {
            return null;
        }

        $data = $result['data'];
        // 下拉数据
        $dropdownList = !empty($data['drop_down_box']) ? $data['drop_down_box'] : [];


        //列表数据
        $records = !empty($data['list']) ? $data['list'] : [];
        return array(
            'data_list' => array(
                'key' => $this->_tableHeader,
                'value' => $records,
                'drop_down_box' => $dropdownList,
            ),
            'page_data' => array(
                'offset' => $params['page'],
                'limit' => intval($params['page_size']),
                'total' => $data['count'],
            )
        );
    }

    /**
     * 添加一条记录
     * @param $params
     * @return array
     */
    public function addOne($params)
    {

        $url = $this->_baseUrl . $this->_addUrl;
        $result = $this->httpRequest($url, $params, 'POST');


        // 确认下返回的数据是否与预期一样
        if (empty($result) || !isset($result['status']))
        {
            return array(false, json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return array($result['status'], $result['message']);
    }

    /**
     * 编辑
     * @return array
     */
    public function editOne($params)
    {
        // 1.调用接口

        $url = $this->_baseUrl . $this->_editUrl;
        $result = $this->httpRequest($url, $params, 'POST');

        // 确认下返回的数据是否与预期一样
        if (empty($result) || !isset($result['status']))
        {
            return array(false, json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return array($result['status'], $result['message']);
    }

    /**
     * 删除一条记录
     * @param array $params = array(
     *      'ids' => string required 记录ID，多个以逗号隔开
     * )
     * @return array = array(
     *      $status => bool 是否成功
     *      $msg    => string 错误信息
     * )
     * @return array
     */
    public function drop(array $params)
    {
        // 1.调用接口
        $url = $this->_baseUrl . $this->_dropUrl;
        $result = $this->httpRequest($url, $params, 'POST');

        // 2.确认返回的数据是否与预期一样
        if (empty($result) || !isset($result['status']))
        {
            return array(false, json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return array($result['status'], $result['message']);
    }

}