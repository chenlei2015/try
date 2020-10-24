<?php

/**
 * 说明：本类只使用php7的MongoDB扩展  不适用php5
 * Class Mongodb
 */
class Mongodb{
    //插入数据
    public function insert($data){
        try{
            $bulk  = new MongoDB\Driver\BulkWrite();
            $result = $bulk->insert($data);
            $id = '';
            foreach($result as $key => $val){
                if($key == 'oid'){
                    $id = $val;
                }
            }
            if($id){
                $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                $manager  = new  MongoDB\Driver\Manager ('mongodb://root:root@localhost:27017');
                $result = $manager->executeBulkWrite('test.listing', $bulk, $writeConcern);//test 为数据库 listing为集合
                if($result->getInsertedCount()){
                    return $id;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }catch (MongoDB\Driver\InvalidArgumentException $e){
            echo "插入失败: ".$e->getMessage();
        }
    }

    //删除
    public function delete($where){
        try{
            $bulk  = new MongoDB\Driver\BulkWrite();
            $bulk->delete($where,array('limit'=>1));// limit 为 1 时，删除第一条匹配数据; limit 为 0 时，删除所有匹配数据
            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            $manager  = new  MongoDB\Driver\Manager ('mongodb://root:root@localhost:27017');
            $result = $manager->executeBulkWrite('test.listing', $bulk, $writeConcern);//test 为数据库 listing为集合
            return $result->getDeletedCount();
        }catch (MongoDB\Driver\InvalidArgumentException $e){
            echo "删除失败: ".$e->getMessage();
        }
    }


    //更新
    public function update($where,$data,$upsert = false){
        try{
            $bulk  = new MongoDB\Driver\BulkWrite();
            $bulk->update($where,array('$set' => $data), array('multi' => true, 'upsert' => $upsert)); //multi=>true 更新所有匹配到的数据 multi=>false 只更新一条数据
            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            $manager  = new  MongoDB\Driver\Manager ('mongodb://root:root@localhost:27017');
            $result = $manager->executeBulkWrite('test.listing', $bulk, $writeConcern);//test 为数据库 listing为集合
            return $result->getModifiedCount();
        }catch (MongoDB\Driver\InvalidArgumentException $e){
            echo "跟新失败: ".$e->getMessage();
        }
    }


    //查询数据
    public function query($where,$options){
        $query = new MongoDB\Driver\Query($where, $options);
        $manager  = new  MongoDB\Driver\Manager('mongodb://root:root@localhost:27017');
        $cursor = $manager->executeQuery('test.listing', $query);//test 为数据库 listing为集合
        $returns = array();
        foreach ($cursor as $doc)
        {
            unset($doc->_id);
            $returns[] = (array)$doc;
        }
        return $returns;
    }

}
