<?php
require_once('Path.php');
require_once("UnitTest.php");
require_once('View_case.php');

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

/**************************************/

require_once("Model.php"); 
require_once("View.php"); 

$show = false;
$test = viewCases();
for ($i=0;$i<count($test);$i++) {
if ($show) {echo "<br>" ; };
$v = $test[$i][0];
$p = $test[$i][1];
$s = $test[$i][2];
$res = $v->show($p,$s,$show); 
$log->logLine ($res);
}

$log->saveTest();

$log->showTest();


?>