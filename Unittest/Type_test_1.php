
<?php
	require_once("UnitTest.php");
	
	$logName = basename(__FILE__, ".php");
	
	$log=new unitTest($logName);
	
	$log->logLine('/* Time related types */');
	
	/**************************************/
	
	$r=checkType(-1,M_DATE); 
	$log->logLine("$r=checkType(-1,M_DATE); ");
	
	$r=checkType('2016-10-25',M_DATE); 
	$log->logLine("$r=checkType('2016-10-25',M_DATE); ");
	
	$r=checkType(' 2016-10-25 12:30:48',M_DATE); 
	$log->logLine("$r=checkType(' 2016-10-25 12:30:48',M_DATE); ");
	
	$r=checkType('1959-05-26',M_DATE); 
	$log->logLine("$r=checkType('1959-05-26',M_DATE); ");
	
	$r=checkType('1959-5-26',M_DATE); 
	$log->logLine("$r=checkType('1959-5-26',M_DATE); ");
	
	$r=checkType('1959-02-31',M_DATE); 
	$log->logLine("$r=checkType('1959-02-31',M_DATE); ");
	
	$r=checkType('2016-02-28',M_DATE); 
	$log->logLine("$r=checkType('2016-02-28',M_DATE); ");
	
	$r=checkType('2016-02-29',M_DATE); 
	$log->logLine("$r=checkType('2016-02-29',M_DATE); ");
	
	$r=checkType('2000-02-29',M_DATE); 
	$log->logLine("$r=checkType('2000-02-29',M_DATE); ");
	
	$r=checkType('2016-10-25 12:30:48',M_TMSTP); 
	$log->logLine("$r=checkType('2016-10-25 12:30:48',M_TMSTP);");
	
	$r=checkType('2016-10-25',M_TMSTP); 
	$log->logLine("$r=checkType('2016-10-25',M_TMSTP);");
	
	$r=checkType('now',M_TMSTP); 
	$log->logLine("$r=checkType('now',M_TMSTP);");
	
	/**************************************/
	
	$log->saveTest();
	
//	$log->showTest();

?>