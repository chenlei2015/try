<?php

use php\oop\first;
use php\oop\second;
use php\oop\third;
use php\oop\outoload;

require_once '../../outoload.php';

ini_set('display_errors','on');

error_reporting(-1);


$first = new first();

$first->who();

$first->identity();

$second = new second();

$second->who();

$second->identity();

