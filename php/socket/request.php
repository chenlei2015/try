<?php
require "socket.php";

$socket = new Socket();
$url="/php/debug/log.php";
$socket->runThreadSOCKET($url);