<?php
ini_set("display_errors","1");//开启报错日志
error_reporting(-1); //所有报错
require "./server/Fedex_model.php";
define('PATH_WSDL',__DIR__.'/wsdl');

$fedex = new Fedex_model();

$fedex->uploadEtdImage();
