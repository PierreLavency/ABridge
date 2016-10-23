<?php
	
	require_once("UnitTest.php");
	$logName = basename(__FILE__, ".php");

	$log=new unitTest($logName);
	
	require_once('Type.php');
	
	$log->logLine('/* standalone test */');
	
	/**************************************/
	
	require_once('Type.php');	
	$X = '1';
									$line = "X = '1';";
									$log->logLine($line);
	$type = M_INT;
									$line = "$type = M_INT;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X=1;
									$line = "X=1;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X=1.5;
									$line = "X=1.5;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);


	$X = '1';
									$line = "X = '1';";
									$log->logLine($line);
	$type = M_FLOAT;
									$line = "$type = M_FLOAT;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X=1;
									$line = "X=1;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X=1.5;
									$line = "X=1.5;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X = '1';
									$line = "X = '1';";
									$log->logLine($line);
	$type = M_STRING;
									$line = "$type = M_STRING;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X=1;
									$line = "X=1;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X=1.5;
									$line = "X=1.5;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	

	$X = 0;
									$line = "X = 0;";
									$log->logLine($line);
	$type = M_BOOL;
									$line = "$type = M_BOOL;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	
	$X = 'x';
									$line = "X = 'x';";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	$X = 'false';
									$line = "X = 'false';";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

	$X = 1;
									$line = "X = 1;";
									$log->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$log->logLine($line);

									
									
$r=checkType(-1,M_INTP); 
	$line = "$r=checkType(-1,M_INTP);";
	$log->logLine($line);
$r=checkType(1,M_INTP); 
	$line = "$r=checkType(1,M_INTP);";
	$log->logLine($line);
$r=checkType(1,M_INTP); 
	$line = "$r=checkType(1,M_INTP);";
	$log->logLine($line);
$r=checkType(0,M_INTP); 
	$line = "$r=checkType(0,M_INTP); ";
	$log->logLine($line);
$r=checkType(1,M_ALNUM); 
	$line = "$r=checkType(1,M_ALNUM);";
	$log->logLine($line);
$r=checkType('1',M_ALNUM); 
	$line = "$r=checkType('1',M_ALNUM);";
	$log->logLine($line);
$r=checkType('A1',M_ALNUM); 
	$line = "$r=checkType('A1',M_ALNUM);";
	$log->logLine($line);
$r=checkType(1,M_ALPHA); 
	$line = "$r=checkType(1,M_ALPHA);";
	$log->logLine($line);
$r=checkType('1',M_ALPHA); 
	$line = "$r=checkType('1',M_ALPHA);";
	$log->logLine($line);
$r=checkType('A1',M_ALPHA); 
	$line = "$r=checkType('A1',M_ALPHA);";
	$log->logLine($line);
$r=checkType('Abb',M_ALPHA); 
	$line = "$r=checkType('Abb',M_ALPHA);";
	$log->logLine($line);

$r=isMtype(M_INT);	
	$line = "$r=isMtype(M_INT);";
	$log->logLine($line);
	
$log->saveTest();

//$log->showTest();

?>	