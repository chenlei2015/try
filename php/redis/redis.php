<?php
/**
 * Created by PhpStorm.
 * User: mandelay
 * Date: 12/03/19
 * Time: 下午 11:15
 */

$redis =new Redis();

$redis->connect('127.0.0.1',6379);

$redis->set('mian',"kkkk");

echo $redis->get('ten');
echo $redis->get('name');
echo $redis->get('wife');
echo $redis->get('mian');

//$redis->del('age');

$redis->close();