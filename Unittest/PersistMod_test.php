<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);
$show = 0;

require_once("PersistMod.php"); 

// create save and get
$pm = new ModBase();

$mod = new Model('students');

$res = $mod->addAttr('name');
	$line = "$res = mod->addAttr('name');"; $z->logLine($line);

$res = $mod->addAttr('surname');
	$line = "$res = mod->addAttr('surname');"; $z->logLine($line);

$res = $mod->addAttr('birthdate',M_TMSTP);
	$line = "$res = mod->addAttr('birthdate',M_TMSTP);"; $z->logLine($line);

$res = $mod->addAttr('tel',M_INT);
	$line = "$res = mod->addAttr('tel',M_INT);"; $z->logLine($line);

$attr_lst = $mod->getAllAttr();
	$line = "attr_lst = mod->getAllAttr();"; $z->logLine($line);

$typ_lst = $mod->getAllAttrTyp();
	$line = "typ_lst = mod->getAllAttr();"; $z->logLine($line);

$res=$pm->saveMod($mod);
	$line = "$res=pm->saveMod(mod);"; $z->logLine($line);

$r = $mod-> getErrLog ();
$z->includeLog($r);	

	
$ins = new Model('students');

$res=$pm->initMod($ins);
	$line = "$res=pm -> initMod(ins);"; $z->logLine($line);

$attr_lst1 = $ins->getAllAttr();
	$line = "attr_lst1 = mod->getAllAttr();"; $z->logLine($line);

$typ_lst1 = $ins->getAllAttrTyp();
	$line = "typ_lst1 = mod->getAllAttr();"; $z->logLine($line);
	
$r = ($attr_lst == $attr_lst1);
	$line = "$r = (attr_lst == attr_lst1);"; $z->logLine($line);

$r = ($typ_lst == $typ_lst1);
	$line = "$r = (typ_lst == typ_lst1);"; $z->logLine($line);


$res1 = $ins->setVal('name','Lavency');
	$line = "$res1 = ins->setVal('name','Lavency');"; $z->logLine($line);
$res2 = $ins->setVal('surname','Pierre');
	$line = "$res2 = ins->setVal('surname','Pierre');"; $z->logLine($line);
$res = $ins->setVal('tel','lavency');
	$line = "$res = ins->setVal('tel','lavency');"; $z->logLine($line);
$res3 = $ins->setVal('tel',123);
	$line = "$res3 = ins->setVal('tel',123);"; $z->logLine($line);
$id = $pm->saveModObj($ins);
	$line = "$id = pm->saveModObj(ins)"; $z->logLine($line);

$r = $ins-> getErrLog ();
$z->includeLog($r);	


$ins = new Model('students',$id);

$res=$pm->initMod($ins);
	$line = "$res=pm -> initMod(ins);"; $z->logLine($line);
	
$res=$pm->initModObj($ins);


$re1  = $ins->getVal('name');
$re2  = $ins->getVal('surname');
$re3  = $ins->getVal('tel');

$t = ($res1 == $re1);
	$line = "$t = (res1 == re1);"; $z->logLine($line);

$t = ($res2 == $re2);
	$line = "$t = (res2 == re2);"; $z->logLine($line);

$t = ($res3 == $re3);
	$line = "$t = (res3 == re3);;"; $z->logLine($line);

$r = $ins-> getErrLog ();
$z->includeLog($r);	

$z->save();

?>
