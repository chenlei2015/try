<?php
header("content-type:text/html;charset=utf-8");
$order = "C:\Users\Yibai\AppData\Local\Programs\Python\Python37\python.exe .\\test.py";
echo shell_exec($order);
//die(shell_exec($order));