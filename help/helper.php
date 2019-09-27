<?php
function debug($result){
    ini_set("display_errors","1");//开启报错日志
    error_reporting(-1);        //所有报错
    error_reporting(E_ERROR);  //所有致命错误
    error_reporting(E_ALL ^ (E_NOTICE | E_STRICT | E_WARNING | E_DEPRECATED));//除了通知、严格模式、警告
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt','11'.PHP_EOL);//清空原来的内容 写入现在的内容
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt','11'.PHP_EOL,FILE_APPEND);//文件内容后追加
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt',json_encode($result).PHP_EOL,FILE_APPEND);
    file_put_contents(Yii::getPathOfAlias('webroot').'/protected/runtime/1.txt',"<?php\n".'return '.var_export($data, true).";\n\n?>",FILE_APPEND);
}

