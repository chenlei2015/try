<?php
ini_set("display_errors", "On");
error_reporting(-1);

class mysql {

    public $source;

    public $config = [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => 'yc_dfs168',
        'database' => 'dfs_edu',
        'charset' => 'utf8', //指定字符集
        'timeout' => 2,  // 可选：连
    ];

    public function __construct()
    {
        $this->source = new swoole_mysql();

    }


    public function execute(){

        $this->source->connect($this->config, function ($db,$result){

            if(!$result){

                var_dump($db->connect_errno, $db->connect_error);die;
            }

            $sql= "select * from crm_admin where id = 1";

            $db->query($sql,function ($link,$result){

                if($result === false){
                    var_dump($link->error,$link->errno);die;
                }elseif($result === true){  //增删改
                    var_dump($link->affected_rows);
                }else{ //查
                    print_r($result);
                }

                $link->close();

            });

        });

        return true;
    }

}

$db = new mysql();

$result = $db->execute();

var_dump($result);

echo 888888;