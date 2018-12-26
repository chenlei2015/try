<?php
$a=[1,2,3,4,5,6,7];
foreach ($a as $k=>&$v){
   $v*=2;
}