<?php
require_once("GenHTML.php"); 
require_once("GenHTML_case.php");
require_once("UnitTest.php");

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);


/**************************************/

$show = false;

for ($i=0;$i<count($test);$i++) {
	
	$show = false;
	if($show) {echo "<br/>\n";}
	$case = $test[$i];
	$r=genFormElem($case[0],$show);
	$line = "$r";
	$log->logLine ($line);
}
	
$log->saveTest();

//$log->showTest();

?>