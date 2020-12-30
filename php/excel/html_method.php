<?php

function export_excel($heads, $datalist, $filename, $field_img_name = array('图片'), $field_img_key = array('')){
    set_time_limit(0);
    ini_set('memory_limit', '500M');
    ini_set('post_max_size', '500M');
    ini_set('upload_max_filesize', '1000M');
    header( "Content-Type: application/vnd.ms-excel; name='excel'" );
    header( "Content-type: application/octet-stream" );
    header( "Content-Disposition: attachment; filename=".$filename );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Pragma: no-cache" );
    header( "Expires: 0" );
    $str = "<html xmlns:c=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"
        \r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type 
        content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
    $str .="<style>tr{height: 50px;}</style>";
    $str .="<table border=1>";
    $str .= "<tr>";
    $line_arr = array();
    foreach ($heads as $line => $title) {
        if (in_array($title, $field_img_name)) {
            $line_arr[] = $line;
            $str .= "<th width='50'>{$title}</th>";
        } else {
            $str .= "<th>{$title}</th>";
        }
    }

    foreach ($datalist as $key=> $rt )
    {
        $str .= "<tr>";
        foreach ( $rt as $k => $v )
        {
            if ((in_array($k, $line_arr) || in_array($k, $field_img_key))) {
                $str .= "<td><img src='{$v}' width='50' height='50' /></td>";
            } elseif(is_numeric($v) && strlen($v) > 9) {
                $str .= "<td width='300' style='vnd.ms-excel.numberformat:@'>".$v."</td>";
            }else{
                if( is_array($v))
                {
                    continue;
                }
                $str .= "<td style='vnd.ms-excel.numberformat:@'>{$v}</td>";
            }
        }
        $str .= "</tr>\n";
    }
    $str .= "</table></body></html>";

    $str = str_replace(",","",$str);
    exit( $str );

}

//主体数据
$inspect_log = require_once './data.php';
//头部列标题
$title =['验货等级','抽检等级','验货编号','采购单号','sku','验货类型','商品图片','验货区域','商品名称','开发类型','供应商名称',
    '有效采购数量','实收数量','不良数','缺货数量','是否异常','验货员','验货时间','申请人','申请时间','不良类型','不良原因','改善措施',
    '验货备注','责任部门','责任人','是否组长验货','验货结果'];
//Excel图片列标题
$field_img_name = array('商品图片');
//图片字段
$field_img_key = array('dev_imgs');
//文件名
$file_name = 'inspect_report_' . date('YmdHis') . '.xls';

export_excel($title,$inspect_log,$file_name,$field_img_name,$field_img_key);
