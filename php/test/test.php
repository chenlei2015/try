<?php
 $edit_before_data  = require './4.php';

 $edit_after_data  = require './3.php';

 $compare_field = [
     'sample_link',//采集链接
     'supplier_code' ,//供应商
     'title_cn',//标题
     'product_category_id',// 分类
     'material_cn',//商品材质
     'use_cn',//商品用途
     'key_words',//关键词
     'sale_points',//买点
     'pack_list',//todo: 包装清单
     'product_brand_attr',//品牌属性
     'product_brand_id',//产品品牌
     'is_logo',//有无logo
     'spu_logistics_attr',//物流属性
     'sku_info',//sku属性
     'sku_pack_info',//sku属性
 ];

 $compare_special_field = [
     'spu_logistics_attr' => [
         'net_weight',
         'gross_weight',
         'size',
     ],
     'sku_info'=>[],
     'sku_pack_info'=>[],
 ];

 function compare_diff($edit_before_data,$edit_after_data,$compare_field,$compare_special_field){
     $diff = [];
     foreach ($compare_field as $filed){
        // var_dump($filed);echo "<br>";
         $before = $edit_before_data[$filed]??null;
         $after  = $edit_after_data[$filed]??null;
        // print_r($after);echo "<br>";
         //字段值为 字符串
         if(is_string($before) && is_string($after) && ($before!==$after)){
            $diff[$filed] = $before;
         }
         // 字段值为 一维数组
         if(!array_key_exists($filed,$compare_special_field) && is_array($before) && is_array(json_decode($after,true))){
             $after = json_decode($after,true);
             if(count($before) != count($after)){
                 $diff[$filed] = implode(',',$before);
             }else{
                $data = array_diff($before,$after);
                if(!empty($data)){
                    $diff[$filed] = implode(',',$before);
                }
             }
         }
         //字段值 为二维数组
         if(array_key_exists($filed,$compare_special_field) && is_array($before) && is_array(json_decode($after,true))){
             if(empty($compare_special_field[$filed])){
                 $after = json_decode($after,true);
                 $exist_sku = array_intersect_key($before,$after);
                 foreach ($exist_sku as $key=>$value){
                     $sku_before  = $before[$key];
                     $sku_after   = $after[$key];
                     foreach ($sku_before as $k=>$v){
                         if(is_string($sku_after[$k]) && is_string($v) &&  ($v != $sku_after[$k])){
                             $diff[$filed][$key][$k] = $v;
                         }
                         if (is_array($sku_after[$k]) && is_array($v)){
                             if(count($sku_after[$k]) != count($v)){
                                 $diff[$filed][$key][$k] = $v;
                             }else{
                                 $data = array_diff($sku_after[$k],$v);
                                 if(!empty($data)){
                                     $diff[$filed][$key][$k] = implode(',',$v);
                                 }
                             }
                         }
                     }
                 }
             }else{
                 $after = json_decode($after,true);
                 foreach ($compare_special_field[$filed] as $sub_filed){
                     $sub_before =  $before[$sub_filed]??null;
                     $sub_after  =  $after[$sub_filed]??null;
                     if(is_string($sub_before) && is_string($sub_after) && ($sub_before!==$sub_after)){
                         $diff[$filed][$sub_filed] = $sub_before;
                     }
                 }
             }
         }
     }
     return $diff;
 }


 $data = compare_diff($edit_before_data,$edit_after_data,$compare_field,$compare_special_field);

 print_r($data);