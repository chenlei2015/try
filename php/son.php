<?php
require 'father.php';
 
 class son extends father{

 	public function a(){
           static::b(88888);
           static::c();
 	}

 	public static function b($a){
           var_dump($a);
 	}

     public  static function c(){
         var_dump(111111111);
     }

 }

 $obj = new son(66666);

 $obj->a();