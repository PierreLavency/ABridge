<?php

use ABridge\ABridge\UnitTest;
use ABridge\ABridge\View\GenHTML;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';
require_once 'GenHTML_case.php';


$logName = basename(__FILE__, ".php");

$log=new UnitTest('C:/Users/pierr/ABridge/Datastore/', $logName, 1);


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
