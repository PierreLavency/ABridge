<?php

use ABridge\ABridge\UnitTest;

require_once 'View_case.php';

$logName = basename(__FILE__, ".php");

$log=new UnitTest($logName, 1);

/**************************************/

$show = false;
$test = viewCases();
for ($i=0; $i<count($test); $i++) {
    if ($show) {
        echo "<br>" ;
    };
    $v = $test[$i][0];
    $p = $test[$i][1];
    $s = $test[$i][2];
    $res = $v->show($s, $show);
    $log->logLine($res);
}

$log->saveTest();

//$log->showTest();
