<?php
use ABridge\ABridge\Log\Logger;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';
require_once 'View_case.php';

$testRun = true;
$log=new Logger();


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
    $res= $v->show($s, $show);
    $log->logLine($res);
}


if ($testRun) {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'View_init_testRun');
} else {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'View_init');
}
