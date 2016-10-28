<?php

require_once("UnitTest.php");

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* Bkey and Mandatory,deletobjetc and  saveMod */');

/**************************************/

// init Meta
require_once("Model.php"); 

$db=getBaseHandler ('fileBase',$logName);
	$line = "db=getBaseHandler ('fileBase',$logName);"; $log->logLine($line);

// load Codes

$ModN= 'Code';

$s=initStateHandler ($ModN,'fileBase',$logName);
	$line = "s=initStateHandler ($ModN,'fileBase',$logName);"; $log->logLine($line);

$db->inject('Class_code_test');	
	$line = "db->inject('Class_code_test');	"; $log->logLine($line);
	
$code1=new Model($ModN,1);
	$line = "code1=new Model($ModN,1);"; $log->logLine($line);

$nme1=$code1->getVal('Name');	
	$line = "$nme1=code1->getVal('Name');"; $log->logLine($line);

// commit 

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);

// change Meta add Bkey

$res=$code1->setBkey('Name',true); 
	$line = "$res=code1->setBkey('Name',true);"; $log->logLine($line);

$res=$code1->isBkey('Name');
	$line = "$res=code1->isBkey('Name');"; $log->logLine($line);

$res=$code1->saveMod();
	$line = "$res=code->saveMod();"; $log->logLine($line);

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);

// test Bkey 

$log->includeLog($code1-> getErrLog ());

$sextype3 = new Model($ModN);
	
$res=$sextype3->setVal('Name','Sexe');	
	$line = "$res=sextype3->setVal('Name','Sexe');"; $log->logLine($line);	

$res=$sextype3->setVal('Name','Type3');	
	$line = "$res=sextype3->setVal('Name','type3');	"; $log->logLine($line);	

$res = $sextype3->setVal('ValueOf',1);
	$line = "$res = sextype3->setVal('ValueOf',1);"; $log->logLine($line);

$id4 = $sextype3->save();
	$line = "$id4 = sextype3->save();"; $log->logLine($line);

$res = $db->commit();
	$line = "$res = db->commit();"; $log->logLine($line);

$log->includeLog($sextype3->getErrLog ());

// test delete 

$sextype3 = new Model($ModN,$id4);
	$line = "sextype3 = new Model($ModN,$id4);"; $log->logLine($line);

$res= $sextype3->delet();
	$line = "$res= sextype3->delet();"; $log->logLine($line);

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);

$id4 = $sextype3->save();
	$line = "$id4 = sextype3->save();"; $log->logLine($line);

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);

$res= $sextype3->delet();
	$line = "$res= sextype3->delet();"; $log->logLine($line);	

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);

// change Meta and test Mdtr

$code4= new Model($ModN);

$res=$code4->setMdtr('Name',true); 
	$line = "$res=code1->setMdtr('Name',true);"; $log->logLine($line);

$res=$code4->isMdtr('Name');
	$line = "$res=code1->isMdtr('Name');"; $log->logLine($line);

$res=$code4->saveMod();
	$line = "$res=code->saveMod();"; $log->logLine($line);
	
$res=$code4->save();
	$line = "$res=code->saveMod();"; $log->logLine($line);
	
$res=$code4->setVal('Name','');	
	$line = "$res=code4->setVal('Name','');"; $log->logLine($line);
	
$res=$code4->save();
	$line = "$res=code->saveMod();"; $log->logLine($line);

$res=$code4->setMdtr('ValueOf',true); 
	$line = "$res=code1->setMdtr('Name',true);"; $log->logLine($line);
	
$res=$code4->setVal('Name','test');	
	$line = "$res=code4->setVal('Name','test');"; $log->logLine($line);

$res=$code4->save();
	$line = "$res=code->saveMod();"; $log->logLine($line);

$res=$code4->setVal('ValueOf',0);	
	$line = "$res=code4->setVal('ValueOf',0);"; $log->logLine($line);
	
$res=$code4->save();
	$line = "$res=code->saveMod();"; $log->logLine($line);

$log->includeLog($code4->getErrLog ());	
	
$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);

	
/**************************************/
	
$log->saveTest();

// $log->showTest();

?>