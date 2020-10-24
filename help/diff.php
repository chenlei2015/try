<?php
$before = inputs_multi_1();

$after = inputs_multi_2();

function diff($before,$after,&$diff = []){
    $before_keys = array_keys($before);
    $after_keys =  array_keys($after);
    $keys  = array_unique(array_merge($before_keys,$after_keys));
    foreach ($keys as $key){
        if(array_key_exists($key,$after) && array_key_exists($key,$before) && is_array($before[$key]) && is_array($after[$key])){
            $result = diff($before[$key],$after[$key],$diff[$key]);
            if(!empty($result)){
                $diff[$key] = $result;
            }else{
                unset($diff[$key]);
            }
        }else{
            if(isset($before[$key]) && isset($after[$key])){
                if($before[$key] !== $after[$key]){
                    $diff[$key] = $after[$key];
                }
            }elseif(isset($before[$key]) && !isset($after[$key])){
                $diff[$key] = null;//被删除
            }elseif(!isset($before[$key]) && isset($after[$key])){
                $diff[$key] = $after[$key];//新添加
            }
        }
    }
    return $diff;
}

$diff = diff($before,$after);

// print_r(json_encode($diff));

function inputs_multi_1(){
    $data = array(
        'id' => 3,
        'spu'=> '201915888',//商品spu
        'category_id'=>'4859',//分类id
        'product_type'=>1,//多属性
        'product_status'=>1,//商品状态
        'en_title'=>'英文名称',//英文名称
        'zh_title'=>'中文名称',//中文名称
        'orgin_img_url'=>'www.baidu.com',//来源
        'description'=>'gdfhfgjhgjgkrttyrdfhfgjjf0000000',//描述
        'height'=>'10',//高
        'width'=>'20',//宽
        'length'=>'30',//长
        'developer'=>'144859',//开发人
        'purchaser'=>'12561',//采购人
        'bind_rule'=> '',//组合规则
        //sku信息
        'sku_info'=>[
            '201915777-01'=>[
                'attribute'=>json_encode([
                    ['variant_name'=>'color','variant_value'=>'blue'],
                    ['variant_name'=>'size','variant_value'=>'XXL'],
                ]),
                'en_title'=>'英文名称-red-XXL',
                'zh_title'=>'中文名称-red-XXL',
                'gross_weight'=>'5.698',
                'net_weight'=>'5.698',
                'purchase_cost'=>'8',

                'zh_customs_declaration'=>'报关中文名',
                'en_customs_declaration'=>'报关英文名',
                'customs_declaration_weight'=>'5.69',
                'customs_declaration_value'=>'5.69',
                'customs_code'=>'hg_8888888',
                'transport_attribute'=>1,
            ],
            '201915777-02'=>[
                'attribute'=>json_encode([
                    ['variant_name'=>'color','variant_value'=>'red'],
                    ['variant_name'=>'size','variant_value'=>'XXL'],
                ]),

                'en_title'=>'英文00000名称-red-XL',
                'zh_title'=>'中文00000名称-red-XL',
                'gross_weight'=>'5.698',
                'net_weight'=>'5.698',
                'purchase_cost'=>'9',

                'zh_customs_declaration'=>'报关中文名',
                'en_customs_declaration'=>'报关英文名',
                'customs_declaration_weight'=>'5.69',
                'customs_declaration_value'=>'5.69',
                'customs_code'=>'hg_8888888',
                'transport_attribute'=>1,
            ],
            '201915777-03'=>[

                'attribute'=>json_encode([
                    ['variant_name'=>'color','variant_value'=>'blue'],
                    ['variant_name'=>'size','variant_value'=>'XL'],
                ]),

                'en_title'=>'英文0000名称-blue-XXL',
                'zh_title'=>'中文0000名称-blue-XXL',
                'gross_weight'=>'5.698',
                'net_weight'=>'5.698',
                'purchase_cost'=>'10',

                'zh_customs_declaration'=>'报关中文名',
                'en_customs_declaration'=>'报关英文名',
                'customs_declaration_weight'=>'5.69',
                'customs_declaration_value'=>'5.69',
                'customs_code'=>'hg_8888888',
                'transport_attribute'=>1,
            ],
            '201915777-04'=>[
                'en_title'=>'英文0000名称-blue-XL',
                'zh_title'=>'中文0000名称-blue-XL',
                'gross_weight'=>'5.698',
                'net_weight'=>'5.698',
                'purchase_cost'=>'11',

                'zh_customs_declaration'=>'报关中文名',
                'en_customs_declaration'=>'报关英文名',
                'customs_declaration_weight'=>'5.69',
                'customs_declaration_value'=>'5.69',
                'customs_code'=>'hg_8888888',
                'transport_attribute'=>1,
            ]
        ],
    );
    return $data;
}

function inputs_multi_2(){
    $data = array(
        'id' => 3,
        'spu'=> '201915777',//商品spu
        'category_id'=>'4859',//分类id
        'product_type'=>1,//多属性
        'product_status'=>1,//商品状态
        'en_title'=>'英文名称',//英文名称
        'zh_title'=>'中文名称',//中文名称
        'orgin_img_url'=>'www.baidu.com',//来源
        'description'=>'gdfhfgjhgjgkrttyrdfhfgjjf0000000',//描述
        'height'=>'10',//高
        'width'=>'20',//宽
        'length'=>'30',//长
        'developer'=>'144859',//开发人
        'purchaser'=>'12561',//采购人
        'bind_rule'=> '',//组合规则
        //sku信息
        'sku_info' => [
            '201915777-01' => [
                'en_title' => '英文0000名称-red-XXL',
                'zh_title' => '中文0000名称-red-XXL',
                'gross_weight'=> '5.698',
                'net_weight'=> '5.698',
                'purchase_cost'=> '8',
                'zh_customs_declaration' => '报关中文名',
                'en_customs_declaration' => '报关英文名',
                'customs_declaration_weight' => '5.69',
                'customs_declaration_value'  => '5.69',
                'customs_code' => 'hg_8888888',
                'transport_attribute' => 1,
            ],
            '201915777-02' => [
                'en_title'=>'英文00000名称-red-XL',
                'zh_title'=>'中文00000名称-red-XL',
                'gross_weight'=>'5.698',
                'net_weight'=>'5.698',
                'purchase_cost'=>'9',

                'zh_customs_declaration' => '报关99中文名',
                'en_customs_declaration' => '报关英文名',
                'customs_declaration_weight' => '5.69',
                'customs_declaration_value'  => '5.69',
                'customs_code' => 'hg_8888888',
                'transport_attribute' => 1,
            ],
            '201915777-03' => [
                'en_title'=>'英文0000名称-blue-XXL',
                'zh_title'=>'中文0000名称-blue-XXL',
                'gross_weight'=>'5.698',
                'net_weight'=>'5.698',
                'purchase_cost'=>'10',

                'zh_customs_declaration'=>'报关中文名',
                'en_customs_declaration'=>'报关英文名',
                'customs_declaration_weight'=>'5.69',
                'customs_declaration_value'=>'5.69',
                'customs_code'=>'hg_8888888',
                'transport_attribute'=>1,
            ],
            '201915777-05' => [
                'en_title'=>'英文0000名称-blue-XL',
                'zh_title'=>'中文0000名称-blue-XL',
                'gross_weight'=>'5.698',
                'net_weight'=>'5.698',
                'purchase_cost'=>'11',
                'zh_customs_declaration'=>'报关中文名',
                'en_customs_declaration'=>'报关英文名',
                'customs_declaration_weight'=>'5.69',
                'customs_declaration_value'=>'5.69',
                'customs_code'=>'hg_8888888',
                'transport_attribute'=>1,
            ]
        ],
    );
    return $data;
}
