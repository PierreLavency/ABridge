<?php

require_once("UnitTest.php");

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* Creating Codes DB */');

/**************************************/

// init Meta
require_once("Model.php"); 

$db=getBaseHandler ('fileBase',$logName);
	$line = "db=getBaseHandler ('fileBase',$logName);"; $log->logLine($line);

$ModN= 'Code';

$s=initStateHandler ($ModN,'fileBase',$logName);
	$line = "s=initStateHandler ($ModN,'fileBase',$logName);"; $log->logLine($line);

$mod = new Model($ModN);
$mod->deleteMod();

$res = $mod->addAttr('Name');
	$line = "$res = mod->addAttr('Name');"; $log->logLine($line);

$ModP=getPathStringMod($ModN);
$res = $mod->addAttr('ValueOf',M_REF,$ModP);
	$line = "$res = mod->addAttr('ValueOf',M_REF,$ModP);"; $log->logLine($line);

$path='/'.$ModN.'/ValueOf';
$res = $mod->addAttr('Values',M_CREF,$path);	
	$line = "$res = mod->addAttr('Values',M_CREF,$path);"; $log->logLine($line);
	
$res = $mod->saveMod();	
	$line = "$res = mod->saveMod();"; $log->logLine($line);	

$log->includeLog($mod-> getErrLog ());	
	
//create father
	
$sex = new Model($ModN);

$res = $sex->setVal('Name','Sexe');
	$line = "$res = sex->setVal('Name','Sexe');"; $log->logLine($line);	
	
$id1 = $sex->save();
	$line = "$id1 = sex->save();"; $log->logLine($line);	
	
$log->includeLog($sex-> getErrLog ());

// create son

$sextype1 = new Model($ModN);

$res = $sextype1->setVal('Name','Male');
	$line = "$res = sextype1->setVal('Name','Male');"; $log->logLine($line);	

$res = $sextype1->setVal('ValueOf',$id1);
	$line = "$res = sextype1->setVal('ValueOf',$id1);"; $log->logLine($line);

$id2 = $sextype1->save();
	$line = "$id2 = sextype1->save();"; $log->logLine($line);
	
$log->includeLog($sextype1-> getErrLog ());
	
// create daughter

$sextype2 = new Model($ModN);

$res = $sextype2->setVal('Name','Female');
	$line = "$res = sextype2->setVal('Name','Female');"; $log->logLine($line);	

$res = $sextype2->setVal('ValueOf',$id1);
	$line = "$res = sextype2->setVal('ValueOf',$id1);"; $log->logLine($line);

$id3 = $sextype2->save();
	$line = "$id3 = sextype2->save();"; $log->logLine($line);


$log->includeLog($sextype2-> getErrLog ());

	
// commit 

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);
	
/**************************************/
	
$log->saveTest();

// $log->showTest();

?>