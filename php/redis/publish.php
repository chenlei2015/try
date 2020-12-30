<?php
//发布
require_once "redisClient.php";
$redis = new redisClient();
$redis->redis->publish('msg','44_'.time());
