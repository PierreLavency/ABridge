
<?php
	require_once("UnitTest.php");
	$logName = basename(__FILE__, ".php");

	$log=new unitTest($logName);

	$log->logLine('/* standalone test */');
	
	/**************************************/
	
	require_once("Handler.php"); 
	$show = false;

		
	$y = getBaseHandler('fileBase');
	$r = (! $y == 0);
		$line = "$r = (! y == 0);"; $log->logLine($line);

	$s = getBaseHandler('fileBase');
	$r = (! $s == 0);
		$line = "$r = (! s == 0);"; $log->logLine($line);

	$r = ($y === $s);
		$line = "$r = (y === s);"; $log->logLine($line);
		
	$s = getBaseHandler('fileBase','test');	
	$r = (! $s == 0);
		$line = "$r = (! s == 0);"; $log->logLine($line);
	
	$r = ($y !== $s);
		$line = "$r = (y !== s);"; $log->logLine($line);

	$y = getBaseHandler('fileBase','test');

	$r = ($y === $s);
		$line = "$r = (y === s);"; $log->logLine($line);

	$s=initStateHandler ('test','fileBase');	
	$r = (! $s == 0);
		$line = "$r = (! s == 0);"; $log->logLine($line);
	
	$y=getStateHandler ('test');
	$r = ($y === $s);
		$line = "$r = (y === s);"; $log->logLine($line);

	$y=getStateHandler ('test1');
	$r = ($y == 0);
		$line = "$r = (y == 0);"; $log->logLine($line);
	
	
	
	
	if ($show){
		var_dump($r);
	}
	
	
	$log->saveTest();
?>