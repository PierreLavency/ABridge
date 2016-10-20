
<?php
	require_once("UnitTest.php");
	$logName = basename(__FILE__, ".php");
	$z=new unitTest($logName);

	require_once("Handler.php"); 
	$show = false;

		
	$y = getBaseHandler('fileBase');
	$r = (! $y == 0);
		$line = "$r = (! y == 0);"; $z->logLine($line);

	$s = getBaseHandler('fileBase');
	$r = (! $s == 0);
		$line = "$r = (! s == 0);"; $z->logLine($line);

	$r = ($y === $s);
		$line = "$r = (y === s);"; $z->logLine($line);
		
	$s = getBaseHandler('fileBase','test');	
	$r = (! $s == 0);
		$line = "$r = (! s == 0);"; $z->logLine($line);
	
	$r = ($y !== $s);
		$line = "$r = (y !== s);"; $z->logLine($line);

	$y = getBaseHandler('fileBase','test');

	$r = ($y === $s);
		$line = "$r = (y === s);"; $z->logLine($line);

	$s=initStateHandler ('test','fileBase');	
	$r = (! $s == 0);
		$line = "$r = (! s == 0);"; $z->logLine($line);
	
	$y=getStateHandler ('test');
	$r = ($y === $s);
		$line = "$r = (y === s);"; $z->logLine($line);

	$y=getStateHandler ('test1');
	$r = ($y == 0);
		$line = "$r = (y == 0);"; $z->logLine($line);
	
	
	
	
	if ($show){
		var_dump($r);
	}
	
	
	$z->save();
?>