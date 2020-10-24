<?php
class mysql_oop {
    public $conn;
    public $error = null;
    public function __construct($config)
    {
        //$config['host'] = 'localhost:3306';  // mysql服务器主机地址
        $conn = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass']);
        if(!$conn){
            $this->error = '连接失败: ' . mysqli_error($conn);
        }else{
            $this->conn = $conn;
        }

    }

    public function db($db){
        if(!$this->error){
            mysqli_query($this->conn , "set names utf8");
            mysqli_select_db($this->conn, $db);
        }
        return $this;
    }

    /**
     * 查询数据较大时 占用内存 较多
     * @param $sql
     * @return array
     */
    public function query($sql){
        $retval = mysqli_query($this->conn,$sql);
        $result =[];
        while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
        {
            $result[] = $row;
        }
        return $result;
    }
    /**
     * 采用生成器 占用内存 较少
     * 查询数据较大时 占用内存 较多
     * @param $sql
     * @return array
     */
    public function cursor($sql){
        $retval = mysqli_query($this->conn,$sql);
        while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
        {
            yield  $row;
        }
    }

    public function query_one($sql){
        $retval = mysqli_query($this->conn,$sql);
        $result = [];
        while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
        {
            $result[] = $row;
        }
        return $result[0];
    }


    public function __destruct(){
        mysqli_close($this->conn);
    }


}