<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* stand alone test*/');


$show = 0;

/**************************************/

require_once("ModBase.php"); 

// create save and get
$s=getBaseHandler ('fileBase',$logName);
	$line = "s=getBaseHandler ('fileBase',$logName);"; $log->logLine($line);
	
$pm = new ModBase($s);

$mod = new Model('students');

$res = $mod->addAttr('name');
	$line = "$res = mod->addAttr('name');"; $log->logLine($line);

$res = $mod->addAttr('surname');
	$line = "$res = mod->addAttr('surname');"; $log->logLine($line);

$res = $mod->addAttr('birthdate',M_TMSTP);
	$line = "$res = mod->addAttr('birthdate',M_TMSTP);"; $log->logLine($line);

$res = $mod->addAttr('tel',M_INT);
	$line = "$res = mod->addAttr('tel',M_INT);"; $log->logLine($line);

$attr_lst = $mod->getAllAttr();
	$line = "attr_lst = mod->getAllAttr();"; $log->logLine($line);

$typ_lst = $mod->getAllTyp();
	$line = "typ_lst = mod->getAllAttr();"; $log->logLine($line);

	
$res=$pm->saveMod($mod);
	$line = "$res=pm->saveMod(mod);"; $log->logLine($line);

$r = $mod-> getErrLog ();
$log->includeLog($r);	

	
$ins = new Model('students');

$res=$pm->restoreMod($ins);
	$line = "$res=pm -> initMod(ins);"; $log->logLine($line);

$attr_lst1 = $ins->getAllAttr();
	$line = "attr_lst1 = mod->getAllAttr();"; $log->logLine($line);

$typ_lst1 = $ins->getAllTyp();
	$line = "typ_lst1 = mod->getAllAttr();"; $log->logLine($line);
	
$r = ($attr_lst == $attr_lst1);
	$line = "$r = (attr_lst == attr_lst1);"; $log->logLine($line);

$r = ($typ_lst == $typ_lst1);
	$line = "$r = (typ_lst == typ_lst1);"; $log->logLine($line);


$res1 = $ins->setVal('name','Lavency');
	$line = "$res1 = ins->setVal('name','Lavency');"; $log->logLine($line);
$res2 = $ins->setVal('surname','Pierre');
	$line = "$res2 = ins->setVal('surname','Pierre');"; $log->logLine($line);
$res = $ins->setVal('tel','lavency');
	$line = "$res = ins->setVal('tel','lavency');"; $log->logLine($line);
$res3 = $ins->setVal('tel',123);
	$line = "$res3 = ins->setVal('tel',123);"; $log->logLine($line);

$id = $pm->saveObj($ins);
	$line = "$id = pm->saveObj(ins);"; $log->logLine($line);

$r = $ins-> getErrLog ();
$log->includeLog($r);	


$ins = new Model('students',$id);

$res=$pm->restoreMod($ins);
	$line = "$res=pm->restoreMod(ins);"; $log->logLine($line);
	
$res=$pm->restoreObj($ins);


$re1  = $ins->getVal('name');
$re2  = $ins->getVal('surname');
$re3  = $ins->getVal('tel');

$t = ($res1 == $re1);
	$line = "$t = (res1 == re1);"; $log->logLine($line);

$t = ($res2 == $re2);
	$line = "$t = (res2 == re2);"; $log->logLine($line);

$t = ($res3 == $re3);
	$line = "$t = (res3 == re3);;"; $log->logLine($line);

$res1 = $ins->setVal('name','lavency');
	$line = "$res1 = ins->setVal('name','lavency');"; $log->logLine($line);

$r = $ins-> getErrLog ();
$log->includeLog($r);	

$id = $pm->saveObj($ins);
	$line = "$id = pm->saveModObj(ins)"; $log->logLine($line);

$ins = new Model('students',$id);
$res=$pm->restoreMod($ins);
	$line = "$res=pm->restoreMod(ins);"; $log->logLine($line);
	
$res=$pm->restoreObj($ins);
	$line = "$res=pm->restoreObj(ins);"; $log->logLine($line);

$re1  = $ins->getVal('name');
	$line = "$re1  = ins->getVal('name');"; $log->logLine($line);
	
$t = ($res1 == $re1);
	$line = "$t = ($res1 == $re1);"; $log->logLine($line);

$res1 = $ins->setVal('name','Lavency');
	$line = "$res1 = ins->setVal('name','Lavency');"; $log->logLine($line);

$r = $ins-> getErrLog ();
$log->includeLog($r);	

$id = $pm->saveObj($ins);
	$line = "$id = pm->saveModObj(ins)"; $log->logLine($line);

$s->commit();
	$line = "s->commit();"; $log->logLine($line);
	
$s->load();
	$line = "s->load();"; $log->logLine($line);

$ins = new Model('students',$id);
$res=$pm->restoreMod($ins);
	$line = "$res=pm->restoreMod(ins);"; $log->logLine($line);
$res=$pm->restoreObj($ins);
	$line = "$res=pm->restoreObj(ins);"; $log->logLine($line);
	
$re1  = $ins->getVal('name');
$re2  = $ins->getVal('surname');
$re3  = $ins->getVal('tel');

$t = ($res1 == $re1);
	$line = "$t = ($res1 == $re1);"; $log->logLine($line);

$t = ($res2 == $re2);
	$line = "$t = ($res2 == $re2);"; $log->logLine($line);

$t = ($res3 == $re3);
	$line = "$t = ($res3 == $re3);;"; $log->logLine($line);
	
/**************************************/

$log->saveTest();

// $log->showTest();

?>
