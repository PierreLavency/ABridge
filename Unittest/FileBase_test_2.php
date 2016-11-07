<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* finders */');

$show = 0;

/**************************************/

require_once("FileBase.php"); 

$test1=['CODE'=> '001', 'SEVERITY'=> 1];
$test2=['CODE'=> '002', 'SEVERITY'=> 2];
$test3=['CODE'=> '003', 'SEVERITY'=> 1];
$test4=['CODE'=> '004', 'SEVERITY'=> 1];


// create save and get

$x = new FileBase($logName);
if ($x-> existsMod ('ERC')) {$x->delMod('ERC');}

$res=$x->newMod('ERC',[]);
	$line = "$res=x->newMod('ERC')"; $log->logLine($line);

$id1=$x->newObj('ERC',$test1);
	$line = "$id1=x->newObj('ERC',test1);"; $log->logLine($line);

$id2=$x->newObj('ERC',$test2);
	$line = "$id2=x->newObj('ERC',test2);"; $log->logLine($line);

$id3=$x->newObj('ERC',$test3);
	$line = "$id2=x->newObj('ERC',test3);"; $log->logLine($line);

$id4=$x->newObj('ERC',$test4);
	$line = "$id2=x->newObj('ERC',test4);"; $log->logLine($line);
	
$res = $x->findObj('ERC','SEVERITY', 1);
	$line = "res = x->findObj('ERC','SEVERITY', 1);"; $log->logLine($line);
	
$r = implode(',',$res);
	$line = "$r = implode(',',res);"; $log->logLine($line);

	
$res = $x->findObj('ERC','CODE', '001');
	$line = "res = x->findObj('ERC','CODE', '001');"; $log->logLine($line);

$r = implode(',',$res);
	$line = "$r = implode(',',res);"; $log->logLine($line);	
	
$res = $x->findObj('ERC','CODE', '005');
	$line = "res = x->findObj('ERC','CODE', '005');"; $log->logLine($line);

$r = implode(',',$res);
	$line = "$r = implode(',',res);"; $log->logLine($line);	
	
/**************************************/	
	

$log->saveTest();

//$log->showTest();



?>
