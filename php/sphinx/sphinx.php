<?php
include './sphinxapi.php'; //包含sphinxapi类
$sphinx= new SphinxClient(); //实例化
$sphinx->SetServer('localhost',9312);//链接
$sphinx->setFilter('is_delete',[0]);
$sphinx->setFilter('state',[1]);
$sphinx->setMatchMode(SPH_MATCH_EXTENDED2);
$res=$sphinx->Query("编码","knowledge");//查询的字段第二参数是你配置文件里面写得规则这里是*就会匹配所有规则
var_dump($res);die;//打印数据