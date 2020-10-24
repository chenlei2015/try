<?php

    /**
     * 也可使用于一维关联数组
     * 比较两个数组的键名，并返回差集：array_diff_key
     * 比较两个数组的键名，并返回交集：array_intersect_key
     */
    function array_diff_intersect_key(){
        $a1=array("a"=>['age'=>188,'core'=>145],"b"=>['age'=>188,'core'=>999],"c"=>['age'=>188,'core'=>145],"d"=>['age'=>168,'core'=>555]);
        //$a2=array("a"=>['age'=>288,'core'=>245],"c"=>['age'=>288,'core'=>245],"b"=>['age'=>288,'core'=>245]);
        $a2=array("a"=>['age'=>298,'core'=>245],"c"=>['age'=>288,'core'=>225]);

        //第一个参数中出现的key 其他参数中未出现的key 返回的值从第一个数组取
//        $result_diff = array_values(array_diff_key($a1,$a2));
//        print_r($result_diff);die;

       //第一个参数中出现的key 其他参数中都出现的key 返回的值从第一个数组取
        $result_intersect = array_intersect_key($a1,$a2);
        print_r($result_intersect);
        print_r(array_values($result_intersect));die;
    }

    print_r(array_diff_intersect_key());die;


    /**
     * php  list()
     */
    function list_test(){
        // list函数是用数组对一列值进行赋值，该函数只用于数字索引的数组，且假定数字索引从0开始。(这句话很重要,是从索引0开始为变量赋值,如果对应的数字索引不存在，则对应位的变量也为空值。)
        $arr = [[11,45],'kkk']; // 必须为索引数组
        list($list,$gids) = $arr;
        var_dump($list,$gids);die;
    }


    /**
     * array_column()
     */

    function array_column_test(){
        $arr = [
            [
                'id' => 1,
                'name' => 'a'
            ],
            [
                'id' => 2,
                'name' => 'b',
            ],
            [
                'id' => 4,
                'name' => 'c'
            ],
            [
                'id' => 3,
                'name' => 'd'
            ]
        ];
        print_r(array_column($arr,'name'));
       //运行结果：Array ( [0] => a [1] => b [2] => c [3] => d )
        print_r(array_column($arr,'name','id'));
       //运行结果：Array ( [1] => a [2] => b [4] => c [3] => d )
        print_r(array_column($arr, null, 'name'));
       //运行结果：Array ( [a] => Array ( [id] => 1 [name] => a ) [b] => Array ( [id] => 2 [name] => b ) [c] => Array ( [id] => 4 [name] => c ) [d] => Array ( [id] => 3 [name] => d ) )
    }

    /**
     * 只能是一维关联数组
     * 只有在第一个数组中出现，且在所有其他输入数组中也出现的键/值对才返回到结果数组中 键和值都必须一样
     * 关联数组的交集 array_intersect_assoc()
     */
    function array_intersect_assoc_test(){
        $fruit1 = array("red"=>"Apple","yellow"=>"Banana","orange"=>"Orange");
        $fruit2 = array("yellow"=>"Pear","red"=>"Apple","purple"=>"Grape");
        $fruit3 = array("green"=>"Watermelon","orange"=>"Orange","red"=>"Apple");
        $intersection = array_intersect_assoc($fruit1, $fruit2, $fruit3);
        var_dump($intersection);
    }

    /**
     *  匿名义函数处理 给定数组键值对中的值
     *  array_walk()
     */
    function array_walk_test(){
        $a = ['a'=>3,'b'=>4];
        $out = 3;
        $key = "d";
        array_walk($a,function (&$v,$k,$out){
            $v = $v*$out;
        },$out);
        print_r($a);
    }

    /**
     * 把第一个参数作为回调函数调用
     * call_user_func()
     */
    function call_user_func_test(){

    }

    function sprintf_test(){
        $tracking_number = 'DD';
        $app_key = 'FF';
        $app_password= 'SS';
        sprintf('/PrintPDFLableServlet.xsv?serverewbcode=%s&username=%s&password=%s',$tracking_number,$app_key,$app_password);
    }