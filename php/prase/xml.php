<?php
ini_set('display_errors','on');

error_reporting(-1);

$xml='../../log/1.xml';

$xml_obj=simplexml_load_file($xml);

$xml_obj->name='chenlei88';

$xml_obj->addAttribute('sex','man');

$xml_obj->addChild('phone',88888888);

echo $xml_obj->phone;

echo $xml_obj->sex;

$xml_str=$xml_obj->asXML();

var_dump($xml_str);

file_put_contents($xml,$xml_str);