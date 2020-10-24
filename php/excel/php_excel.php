<?php

/**
 * 导出Excel
 * @author 14485 2020-03-26
 */
function export_inspect_log(){
    error_reporting(0);
    //主体数据
    $inspect_log = require_once './data.php';
    //数据字段
    $index_field = ['inspect_grade','spot_grade', 'apply_no', 'po', 'sku', 'inspect_type', 'dev_imgs', 'inspect_detail_address', 'title_cn', 'devp_type', 'supplier_name',
        'real_purchase_num', 'real_collect_num', 'unqualify_num', 'short_supply_num', 'is_abnormal', 'inspector', 'product_inspect_time', 'applicant', 'purchase_apply_time',
        'unqualify_type', 'unqualify_reason', 'improve_measure', 'inspect_remark', 'duty_dept', 'duty_user', 'is_lead_inspect', 'inspect_result'];
    //单元格列
    $unit_title =  ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB'];
    //头部列标题
    $title = ['验货等级','抽检等级','验货编号','采购单号','sku','验货类型','商品图片','验货区域','商品名称','开发类型','供应商名称',
        '有效采购数量','实收数量','不良数','缺货数量','是否异常','验货员','验货时间','申请人','申请时间','不良类型','不良原因','改善措施',
        '验货备注','责任部门','责任人','是否组长验货','验货结果'];
    $head_data = array_combine($unit_title,$title);

    //引入PHP EXCEL类
    require APPPATH . "third_party/PHPExcel.php";
    require APPPATH . "third_party/Handle_Excel.php";
    $objPHPExcel = new PHPExcel();
    $handle_excel = new Handle_Excel();
    $handle_excel->excel_init_set($objPHPExcel);//初始设置


    //设置Excel表的头部列标题
    foreach ($head_data as $k => $v) {
        if(in_array($k,$unit_title)){
            $objPHPExcel->getActiveSheet()->setCellValue($k . 1, $v);//设置标题
        }
    }


    //设置Excel表的数据内容
    $startRow = 2;
    foreach ($inspect_log as $row){
        foreach ($index_field as $key =>$value){
                $objPHPExcel->getActiveSheet()->setCellValue($unit_title[$key].$startRow, $row[$value].'\t');
        }
        $objPHPExcel->getActiveSheet()->getRowDimension($startRow)->setRowHeight(30);//设置每列宽度
        $startRow++;
    }

    //Excel表在浏览器输出
    $file_name = 'inspect_report_' . date('YmdHis') . '.xls';
    $write = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header("Pragma: public");
    //header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header("Content-Disposition:attachment;filename=" . $file_name);
    header("Content-Transfer-Encoding:binary");
    ob_clean();
    $write->save('php://output');
}
