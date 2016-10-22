
<?php
	require_once("UnitTest.php");
	
	$logName = basename(__FILE__, ".php");
	
	$log=new unitTest($logName);
	
	$log->logLine('/* standalone test */');
	
	/**************************************/
	
	$r = $log->setVerbatim (1);
									$line = "$r = ...setVerbatim (1);";
									$log->logLine($line);
	

	/**************************************/
	
	$log->saveTest();
	
//	$log->showTest();

?>