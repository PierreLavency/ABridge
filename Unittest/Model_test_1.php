<?php
// test integration with logger for errors


require_once("Model.php"); 
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);

$x=new Model("ErrorLogging");

$r=$x->getModName() ;
	// logging result
	$xs = "$r=..->getModName() ;";
	$z->logLine ($xs);

$r = $x->setVal('id',2);
	// logging result
	$xs = "$r = x->setVal('id',2);";
	$z->logLine ($xs);

$r = $x->getVal('notexist');
	// logging result
	$xs = "$r = x->getVal('notexist');";
	$z->logLine ($xs);	

$r = $x->setTyp('notexist',M_INT);
	// logging result
	$xs = "$r = x->setTyp('notexist','int');";
	$z->logLine ($xs);

$r = $x->addAttr('id');
	// logging result
	$xs = "$r = x->addAttr('id');";
	$z->logLine ($xs);
	
$r = $x->delAttr('id');
	// logging result
	$xs = "$r = x->delAttr('id');";
	$z->logLine ($xs);

$r = $x->delAttr('notexist');
	// logging result
	$xs = "$r = x->delAttr('notexist');";
	$z->logLine ($xs);
	
	
$r = $x-> getErrLog ();


$z->includeLog($r);
	
$z->save();
/*
$z->show();
*/
?>