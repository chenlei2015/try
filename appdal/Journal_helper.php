<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 报表助手类
 * Class journal_httper
 */

class Journal_helper
{
    /**
     * ci基类控制器对象
     * @var CI_Controller
     */
    private $ciObj;

    private static $instance;

    private $_modelObj;

    public function __construct()
    {
        $this->ciObj    = &get_instance();
        self::$instance = &$this;
    }


    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * 设置数据实体model
     * @param $model
     */
    public  function setTableModel($table_model){
        $this->_modelObj = $table_model;
    }

    /**
     * 导入数据
     */
    public function import($params){
        //下拉框列表数据
        if(method_exists($this->_modelObj, 'JournalBoxList')){
            $this->_modelObj->journalBoxList = $this->_modelObj->JournalBoxList();
        }

        // 读取Excel内容
        $this->ciObj->load->helper('Excel');
        $excelHelper = new Excel_helper();
        $excelHelper->loadFile($params['file_path']);

        // 标题与字段名对应关系
        $titleFieldMap = $this->_modelObj->getTitleFieldMap();
        $fieldTitleMap = array_flip($titleFieldMap);
        $excelHelper->readTitle($titleFieldMap, 1);

        //保存绝对路径
        $importFailPath = COMMON_UPLOAD_PATH.'journal/'.$this->_modelObj->journalName.'/fail/'.date("Ym", time()).'/';

        //保存相对路径
        $importFailDownloadPath = '/end/upload/journal/'.$this->_modelObj->journalName.'/fail/'.date("Ym", time()).'/';

        //文件名
        $importFailFileName = $this->_modelObj->journalName.'_'.date("YmdHis").".csv";

        // 第几行开始
        $rowNum = 1;
        $success_count = 0; // 更新或新增成功条数
        $fail_count = 0;    // 更新或新增失败 及数据验证失败 总条数
        $cvsHeaderExist = false; // cvs错误提示 头部是否已写入
        while ($rows = $excelHelper->readRows(30))
        {
            $itemListPass = [];
            $itemListFail = [];
            // 转换、验证字段
            foreach ($rows as $row)
            {
                $rowNum++;//行数
                $allEmpty = true;
                // 检查是否都为空行
                foreach ($row as $k => $v)
                {
                    $row[$k] = trim($v);
                    if (preg_match("/time$/",$k) && !empty($v)){
                        $row[$k] = gmdate("Y-m-d H:i:s", \PHPExcel_Shared_Date::ExcelToPHP($v));
                    }else{
                        $row[$k] = trim($v);
                    }

                    if (!empty($v))
                    {
                        $allEmpty = false;
                    }
                }

                // 遇到空行，退出
                if ($allEmpty)
                {
                    continue;
                }

                $validateData = $this->_modelObj->importData($row);

                if(!empty($validateData['passData'])){
                    $itemListPass[] = $validateData['passData'][0];
                }

                if(!empty( $validateData['failData'])){
                    $itemListFail[] = $validateData['failData'][0];
                }
            }

            //区分新增与更新数据 并保存与更新
            if(!empty($itemListPass)){
                $result = $this->_modelObj->groupUpdateInsertData($itemListPass);
                if($result['status']){
                    //保存及修改成功
                    $success_count += $result['count'];
                }else{
                    //保存及修改失败 准备保存到csv中
                    foreach ($itemListPass as $item){
                        array_walk($fieldTitleMap,function (&$v,$k,$item){
                            $v = $item[$k];
                        },$item);

                        $fieldTitleMap['errorMsg'] = "更新或新增失败,请重新导入即可";
                        array_push($itemListFail,$fieldTitleMap);
                        unset($fieldTitleMap['errorMsg']);
                    }
                }
            }

            //把验证失败的信息保存到csv文件 以供下载
            if(!empty($itemListFail)){
                $fail_count += count($itemListFail);
                $headTitle = array_keys($titleFieldMap);
                array_push($headTitle,'错误提示');
                $this->saveImportFailData($importFailPath,$importFailFileName,$headTitle,$itemListFail,$cvsHeaderExist);
            }
            $cvsHeaderExist = true;
        }

        return ['failFailUriCsv' =>$fail_count?$importFailDownloadPath.$importFailFileName:"","fail_total"=>$fail_count,"success_total" =>$success_count];
    }

    /**
     * 保存导入失败数据到CSV
     */
    public function saveImportFailData($relativePath,$filename,$headTitle,$data,$cvsHeaderExist){
        if(!empty($data)){
            $fileName = $relativePath.$filename;
            if(!file_exists($fileName)) {
               if (!is_dir($relativePath)) {
                   mkdir($relativePath, 0777, true);
               }
               $fileName = $relativePath . $filename;
            }
            $file = fopen($fileName, "a+");

            //写入标题头
            if(!$cvsHeaderExist){
                fputcsv($file, $headTitle);
            }
            //写入数据
            foreach ($data as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
        }
    }



}