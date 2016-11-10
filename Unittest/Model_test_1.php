<?php
// test integration with logger for errors

require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* integration with error logger */');

/**************************************/

require_once("Model.php"); 
$x=new Model("ErrorLogging");

$r=$x->getModName() ;
	// logging result
	$xs = "$r=..->getModName() ;";
	$log->logLine ($xs);

$r = $x->setVal('id',2);
	// logging result
	$xs = "$r = x->setVal('id',2);";
	$log->logLine ($xs);

$r = $x->getVal('notexist');
	// logging result
	$xs = "$r = x->getVal('notexist');";
	$log->logLine ($xs);	

$r = $x->setTyp('notexist',M_INT);
	// logging result
	$xs = "$r = x->setTyp('notexist','int');";
	$log->logLine ($xs);

$r = $x->addAttr('id');
	// logging result
	$xs = "$r = x->addAttr('id');";
	$log->logLine ($xs);
	
$r = $x->delAttr('id');
	// logging result
	$xs = "$r = x->delAttr('id');";
	$log->logLine ($xs);

$r = $x->delAttr('notexist');
	// logging result
	$xs = "$r = x->delAttr('notexist');";
	$log->logLine ($xs);
	
	
$r = $x-> getErrLog ();


$log->includeLog($r);
	
$log->saveTest();

//$log->showTest();

?>