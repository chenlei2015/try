<?php
// 插入数据
function insert(){
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $bulk = new MongoDB\Driver\BulkWrite;
//        $bulk->insert(['x' => '1', 'name'=>'菜鸟教程', 'url' => 'http://www.runoob.com']);
//        $bulk->insert(['x' => 2, 'name'=>'Google', 'url' => 'http://www.google.com']);
//        $bulk->insert(['x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com']);
    $bulk->insert(['_id'=>'227833','x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com']);
    $manager->executeBulkWrite('test.site', $bulk);
}

//查询数据
function select(){
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $filter = ['x' => ['$gt' => 1]];
    $options = [
        'projection' => ['_id' => 0],
        'sort' => ['x' => -1],
    ];
    // 查询数据
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('test.site', $query);

    foreach ($cursor as $document) {
        print_r((array)$document);
    }
}
//更新
function update(){
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['x' => 2],
        ['$set' => ['name' => '菜鸟工具', 'url' => 'tool.runoob.com']],
        ['multi' => false, 'upsert' => false]
    );
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
    $result = $manager->executeBulkWrite('test.site', $bulk, $writeConcern);
}

//删除
function delete(){
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $bulk = new MongoDB\Driver\BulkWrite;
    //$bulk->delete(['x' => 1], ['limit' => 1]);   // limit 为 1 时，删除第一条匹配数据
    //$bulk->delete(['x' => 2], ['limit' => 0]);   // limit 为 0 时，删除所有匹配数据
    $bulk->delete(['x'=>['$gte' =>1 ]], ['limit' => 0]);   // limit 为 0 时，删除所有匹配数据
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
    $result = $manager->executeBulkWrite('test.site', $bulk, $writeConcern);
}