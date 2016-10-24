<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* standalone test */');

$show = 0;

/**************************************/

require_once("FileBase.php"); 

$test1=['CODE'=> '001', 'SEVERITY'=> 1];
$test2=['CODE'=> '002', 'SEVERITY'=> 2];
$test3=['CODE'=> '001', 'SEVERITY'=> 0];

// create save and get

$x = new FileBase($logName);

$res=$x->newMod('ERC');
	$line = "$res=x->newMod('ERC')"; $log->logLine($line);

$id1=$x->newObj('ERC',$test1);
	$line = "$id1=x->newObj('ERC',test1);"; $log->logLine($line);

$id2=$x->newObj('ERC',$test2);
	$line = "$id2=x->newObj('ERC',test2);"; $log->logLine($line);

	
$res=$x->commit();
	$line = "$res=x->commit();"; $log->logLine($line);

$y= new FileBase($logName);

$y->load();

$res1=$y->getObj('ERC',$id1);
	$line = "res1=y->getObj('ERC',$id1);"; $log->logLine($line);
	
$t = ($res1 == $test1);
	$line = "$t = (res1 == test1);"; $log->logLine($line);
	
$res2=$y->getObj('ERC',$id2);
	$line = "res2=y->getObj('ERC',$id2);"; $log->logLine($line);

$t = ($res2 == $test2);
	$line = "$t = (res2 == test2);"; $log->logLine($line);

// update save and get

$res= $y->putObj('ERC',$id1,$test3);
	$line = "$res= y->putObj('ERC',$id1,test3);"; $log->logLine($line);

$res=$y->commit();
	$line = "$res=y->commit();"; $log->logLine($line);

$y = new FileBase($logName);

$y->load();

$res3=$y->getObj('ERC',$id1);
	$line = "res3=y->getObj('ERC',$id1);"; $log->logLine($line);
	
$t = ($res3 == $test3);
	$line = "$t = (res3 == test3);"; $log->logLine($line);

// delete save and get


$res=$y->delObj('ERC',$id2);
	$line = "$res=y->delObj('ERC',$id2);"; $log->logLine($line);

$res=$y->commit();
	$line = "$res=y->commit();"; $log->logLine($line);
	
$y = new FileBase($logName);

$y->load();

$res=$y->getObj('ERC',$id2);
	$line = "$res=y->getObj('ERC',$id2);"; $log->logLine($line);

/* shoudl return false */

/* META DATA */

$testm1= ['attr_lst'=>['CODE','SEVERITY'],'typ_lst'=>['CODE'=>'m_string','SEVERITY'=>'m_id']];

// create save and get

$x = new FileBase($logName);

$res=$x->newMod('ERC2',$testm1);
	$line = "$res=x->newMod('ERC2',testm1);"; $log->logLine($line);
	
$res=$x->commit();
	$line = "$res=x->commit();"; $log->logLine($line);
	
$y = new FileBase($logName);

$y->load();

$res1=$y->getMod('ERC2');
	$line = "res1=y->getMod('ERC2');"; $log->logLine($line);
	
$t=($testm1 == $res1); 
	$line = "$t=(testm1 == res1);"; $log->logLine($line);

// delete save and get

$res=$y->delMod('ERC2');
	$line = "$res=y->delMod('ERC2');"; $log->logLine($line);
	
$res=$y->commit();
	$line = "$res=y->commit();"; $log->logLine($line);

$y = new FileBase($logName);

$y->load();

$res1=$y->getMod('ERC2');
	$line = "res1=y->getMod('ERC2');"; $log->logLine($line);

// update save and get
$res=$y->newMod('ERC');
	$line = "$res=y->newMod('ERC');"; $log->logLine($line);
	
$res=$y->putMod('ERC',$testm1);
	$line = "res=y->putMod('ERC',testm1);"; $log->logLine($line);

$res=$y->commit();
	$line = "$res=y->commit();"; $log->logLine($line);
	
$y = new FileBase($logName);

$y->load();

$res1=$y->getMod('ERC');
	$line = "res1=y->getMod('ERC');"; $log->logLine($line);
	
$t=($res1 == $testm1); 
	$line = "$t=(res1 == testm1); "; $log->logLine($line);
	

$log->saveTest();

// $log->showTest();



?>
