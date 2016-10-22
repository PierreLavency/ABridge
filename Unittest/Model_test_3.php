<?php
// test integration with persistence


require_once("Model.php"); 
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);

$db=getBaseHandler ('fileBase','ModBase_test');
	$line = "db=getBaseHandler ('fileBase','ModBase_test');"; $z->logLine($line);
$db->load();
	$line = "db->load();"; $z->logLine($line);

$s=initStateHandler ('students','fileBase','ModBase_test');
	$line = "s=initStateHandler ('students','fileBase','ModBase_test');"; $z->logLine($line);




	
$x=new Model('students',1);

$res = implode(',', $x->getAllAttr());
	$line = "$res = implode(',', x->getAllAttr());"; $z->logLine($line);
	
$re1  = $ins->getVal('name');
	$line = "$re1  = ins->getVal('name');"; $z->logLine($line);
$re2  = $ins->getVal('surname');
	$line = "$re2  = ins->getVal('surname');"; $z->logLine($line);
$re3  = $ins->getVal('tel');
	$line = "$re3  = ins->getVal('tel');"; $z->logLine($line);

$r = $x-> getErrLog ();
$z->includeLog($r);
	
$ins=new Model('students');
	
$res1 = $ins->setVal('name','Arnould');
	$line = "$res1 = ins->setVal('name','Arnould');"; $z->logLine($line);
$res2 = $ins->setVal('surname','Dominique');
	$line = "$res2 = ins->setVal('surname','Dominique');"; $z->logLine($line);
$res3 = $ins->setVal('tel',123);
	$line = "$res3 = ins->setVal('tel',123);"; $z->logLine($line);	

$res=$ins->save();
	$line = "$res=ins->save();"; $z->logLine($line);	

$r = $ins-> getErrLog ();
$z->includeLog($r);

		
$ins=new Model('students');
	
$res1 = $ins->setVal('name','Lavency');
	$line = "$res1 = ins->setVal('name','Lavency');"; $z->logLine($line);
$res2 = $ins->setVal('surname','Renaud1');
	$line = "$res2 = ins->setVal('surname',Renaud1');"; $z->logLine($line);
$res3 = $ins->setVal('tel',321);
	$line = "$res3 = ins->setVal('tel',321);"; $z->logLine($line);	

$res2=$ins->setVal('surname','Renaud');
	$line = "$res2=ins->setVal('surname','Renaud');"; $z->logLine($line);

$res=$ins->save();
	$line = "$res=ins->save();"; $z->logLine($line);	
	
$r = $ins-> getErrLog ();
$z->includeLog($r);

$db->save();
	
$z->save();


 // $z->show();

?>