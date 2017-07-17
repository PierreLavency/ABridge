<?php

use  ABridge\ABridge\UnitTest;
use ABridge\ABridge\View\GenHTML;


$logName = basename(__FILE__, ".php");

$log=new UnitTest($logName, 1);


/**************************************/

$show = false;
$test = GenHTLMCases();

for ($i=0; $i<count($test); $i++) {
    $show = false;
    if ($show) {
        echo "<br/>\n";
    }
    $case = $test[$i];
    $r=GenHTML::genFormElem($case[0], $show);
    $log->logLine($r);
}
    
$log->saveTest();

//$log->showTest();
