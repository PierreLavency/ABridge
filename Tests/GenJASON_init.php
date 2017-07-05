<?php
require_once("GenJASON.php");
require_once("GenJASON_case.php");
require_once("UnitTest.php");

$logName = basename(__FILE__, ".php");

$log=new UnitTest($logName,1);

/**************************************/

$show = false;
$test = GenJasonCases();

for ($i=0; $i<count($test); $i++) {
    if ($show) {
        echo "<br/>\n";
    }
    $case = $test[$i];
    $h= new Model($case[0], $case[1]);
    $r=genJason($h, $show, false, $case[2]);
    $log->logLine($r);
}
    
$log->saveTest();

//$log->showTest();
