<?php

require_once("UnitTest.php");

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* integration with ModeBase and file Persistence */');

/**************************************/

require_once("Model.php"); 

$db=getBaseHandler ('fileBase',$logName);
	$line = "db=getBaseHandler ('fileBase',$logName);"; $log->logLine($line);

$s=initStateHandler ('students','fileBase',$logName);
	$line = "s=initStateHandler ('students','fileBase',$logName);"; $log->logLine($line);

/********/	
$mod = new Model('students');

$res = $mod->addAttr('name');
	$line = "$res = mod->addAttr('name');"; $log->logLine($line);

$res = $mod->addAttr('surname');
	$line = "$res = mod->addAttr('surname');"; $log->logLine($line);

$res = $mod->addAttr('birthdate',M_TMSTP);
	$line = "$res = mod->addAttr('birthdate',M_TMSTP);"; $log->logLine($line);

$res = $mod->addAttr('tel',M_INT);
	$line = "$res = mod->addAttr('tel',M_INT);"; $log->logLine($line);

$res = $mod->saveMod();	
	$line = "$res = mod->saveMod();"; $log->logLine($line);	

$ins = new Model('students');

$res1 = $ins->setVal('name','Lavency');
	$line = "$res1 = ins->setVal('name','Lavency');"; $log->logLine($line);
$res2 = $ins->setVal('surname','Pierre');
	$line = "$res2 = ins->setVal('surname','Pierre');"; $log->logLine($line);
$res3 = $ins->setVal('tel',123);
	$line = "$res3 = ins->setVal('tel',123);"; $log->logLine($line);

$id = $ins->save();
	$line = "$id = ins->save();"; $log->logLine($line);

$r = $ins-> getErrLog ();
$log->includeLog($r);	

/*******/	

$x=new Model('students',1);

$res = implode(',', $x->getAllAttr());
	$line = "$res = implode(',', x->getAllAttr());"; $log->logLine($line);
	
$re1  = $ins->getVal('name');
	$line = "$re1  = ins->getVal('name');"; $log->logLine($line);
$re2  = $ins->getVal('surname');
	$line = "$re2  = ins->getVal('surname');"; $log->logLine($line);
$re3  = $ins->getVal('tel');
	$line = "$re3  = ins->getVal('tel');"; $log->logLine($line);

$r = $x-> getErrLog ();
$log->includeLog($r);
	
$ins=new Model('students');
	
$res1 = $ins->setVal('name','Arnould');
	$line = "$res1 = ins->setVal('name','Arnould');"; $log->logLine($line);
$res2 = $ins->setVal('surname','Dominique');
	$line = "$res2 = ins->setVal('surname','Dominique');"; $log->logLine($line);
$res3 = $ins->setVal('tel',123);
	$line = "$res3 = ins->setVal('tel',123);"; $log->logLine($line);	

$res=$ins->save();
	$line = "$res=ins->save();"; $log->logLine($line);	

$r = $ins-> getErrLog ();
$log->includeLog($r);


$ins=new Model('students');
	
$res1 = $ins->setVal('name','Lavency');
	$line = "$res1 = ins->setVal('name','Lavency');"; $log->logLine($line);
$res2 = $ins->setVal('surname','Renaud1');
	$line = "$res2 = ins->setVal('surname',Renaud1');"; $log->logLine($line);
$res3 = $ins->setVal('tel',321);
	$line = "$res3 = ins->setVal('tel',321);"; $log->logLine($line);	

$res2=$ins->setVal('surname','Renaud');
	$line = "$res2=ins->setVal('surname','Renaud');"; $log->logLine($line);

$res=$ins->save();
	$line = "$res=ins->save();"; $log->logLine($line);	
	
$r = $ins-> getErrLog ();
$log->includeLog($r);

$db->commit();
	

/**************************************/
	
$log->saveTest();

//$log->showTest();

?>