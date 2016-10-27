<?php

require_once("UnitTest.php");

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* Handling M_REF and M_CREF */');

/**************************************/

// init Meta
require_once("Model.php"); 

$db=getBaseHandler ('fileBase',$logName);
	$line = "db=getBaseHandler ('fileBase',$logName);"; $log->logLine($line);

$ModN= 'Code';

$s=initStateHandler ($ModN,'fileBase',$logName);
	$line = "s=initStateHandler ($ModN,'fileBase',$logName);"; $log->logLine($line);

$mod = new Model($ModN);

$res = $mod->addAttr('Name');
	$line = "$res = mod->addAttr('Name');"; $log->logLine($line);
// an error
$res = $mod->addAttr('ValueOf',M_REF);
	$line = "$res = mod->addAttr('ValueOf',M_REF);"; $log->logLine($line);
// corrected	
$res = $mod->addAttr('ValueOf',M_REF,$ModN);
	$line = "$res = mod->addAttr('ValueOf',M_REF,$ModN);"; $log->logLine($line);

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

// an error 
$res = $sex ->checkRef('ValueOf',2);
	$line = "$res = sex ->checkRef('ValueOf',2);"; $log->logLine($line);
// a good one 
$res = $sex ->checkRef('ValueOf',1);
	$line = "$res = sex ->checkRef('ValueOf',1);"; $log->logLine($line);
	
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

$id_3 = $sextype2->getId();
	$line = "$id_3 = sextype2->getId();"; $log->logLine($line);

$id_3 = $sextype2->getVal('id');
	$line = "$id_3 = sextype2->getVal('id');"; $log->logLine($line);

$log->includeLog($sextype2-> getErrLog ());

// a null ref 

$sextype3 = new Model($ModN);

$res = $sextype3->setVal('Name','Bi');
	$line = "$res = sextype3->setVal('Name','Bi');"; $log->logLine($line);	

$res = $sextype3->setVal('ValueOf',$id1);
	$line = "$res = sextype3->setVal('ValueOf',$id1);"; $log->logLine($line);

$res = $sextype3->setVal('ValueOf',0);
	$line = "$res = sextype3->setVal('ValueOf',$id1);"; $log->logLine($line);
	
$id4 = $sextype3->save();
	$line = "$id4 = sextype2->save();"; $log->logLine($line);

$log->includeLog($sextype3-> getErrLog ());

// get children

$res= $sex->getVal('Values');
	$line = "res= sex->getVal('Values');"; $log->logLine($line);
$res = implode (',',$res);	
	$line = "$res = implode (',';res);"; $log->logLine($line);
	
$res= $sextype1->getVal('Values');
	$line = "res= sextype1->getVal('Values');"; $log->logLine($line);
$res = implode (',',$res);	
	$line = "$res = implode (',';res);"; $log->logLine($line);	

// some errors 	
	
$res = $sextype1->setVal('Values',$id1);
	$line = "$res = sextype1->setVal('Values',$id1);"; $log->logLine($line);

$res = $sextype1->setVal('ValueOf',5);
	$line = "$res = sextype1->setVal('ValueOf',5);"; $log->logLine($line);	

$res= $sextype1->getVal('ValueOf');
	$line = "$res= sextype1->getVal('ValueOf');"; $log->logLine($line);
	
$log->includeLog($sextype1-> getErrLog ());
	
// commit 

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);
	
/**************************************/
	
$log->saveTest();

//$log->showTest();

?>