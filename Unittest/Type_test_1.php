
<?php
	require_once("UnitTest.php");
	
	$logName = basename(__FILE__, ".php");
	
	$log=new unitTest($logName);
	
	$log->logLine('/* Time related types */');
	
	/**************************************/
	
	$r=checkType(-1,M_DATE); 
	$log->logLine("$r=checkType(-1,M_DATE); ");
	
	$r=checkType('25-10-2016',M_DATE); 
	$log->logLine("$r=checkType('25-10-2016',M_DATE); ");
	
	$r=checkType(' 25-10-2016 12:30:48',M_DATE); 
	$log->logLine("$r=checkType(' 25-10-2016 12:30:48',M_DATE); ");
	
	$r=checkType('26-05-1959',M_DATE); 
	$log->logLine("$r=checkType('26-05-1959',M_DATE); ");
	
	$r=checkType('26-5-1959',M_DATE); 
	$log->logLine("$r=checkType('26-5-1959',M_DATE); ");
	
	$r=checkType('31-02-1959',M_DATE); 
	$log->logLine("$r=checkType('31-02-1959',M_DATE); ");
	
	$r=checkType('28-02-2016',M_DATE); 
	$log->logLine("$r=checkType('28-02-2016',M_DATE); ");
	
	$r=checkType('29-02-2016',M_DATE); 
	$log->logLine("$r=checkType('29-02-2016',M_DATE); ");
	
	$r=checkType('29-02-2000',M_DATE); 
	$log->logLine("$r=checkType('29-02-2000',M_DATE); ");
	
	$r=checkType('25-10-2016 12:30:48',M_TMSTP); 
	$log->logLine("$r=checkType('25-10-2016 12:30:48',M_TMSTP);");
	
	$r=checkType('25-10-2016',M_TMSTP); 
	$log->logLine("$r=checkType('25-10-2016',M_TMSTP);");
	
	$r=checkType('now',M_TMSTP); 
	$log->logLine("$r=checkType('now',M_TMSTP);");
	
	/**************************************/
	
	$log->saveTest();
	
//	$log->showTest();

?>