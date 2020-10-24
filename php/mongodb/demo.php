<?php
    require './Mongodb.php';

    $mongo = new Mongodb();
    save($mongo);
//    del($mongo);
//    up($mongo);
//    select($mongo);

    //新增数据
    function save($mongo){
        $data = array(
            'account_id'=>308,
            'item_id'=>date('Y-m-d H:i:s')
        );
        $result = $mongo->insert($data);
        var_dump($result);
    }


    //删除数据
    function del($mongo){
        $where =  array('account_id'=>308);
        //$where =  array('account_id'=>['$gt'=>0]); // account_id >0
        $result= $mongo->delete($where);
        var_dump($result);
    }

    //更新数据
    function up($mongo){
        $where =  array('account_id'=>300);
        //$where =  array('account_id'=>['$gt'=>0]); // account_id >0
        $data = array('item_id'=>99);
        $result= $mongo->update($where,$data);
        var_dump($result);
    }

    //查询实例
    function select($mongo){
        $where =  array('account_id'=>300);
        //$where   = array('account_id'=>array('$gt'=>0));// account_id >0
        $options = array(
            //相当于mysql的offset
            'skip' =>0,
            //排序 -1 降序 1 升序
            'sort' =>array('account_id'=> -1),
            //相当于mysql的limit
            'limit' => 999999999999,
            //要查询的字段
            'projection' => array(
                '_id' => 1,
                'item_id' => 1,
                'account_id' => 1
            ),
        );
        $result = $mongo->query($where,$options);
        print_r($result);
    }
