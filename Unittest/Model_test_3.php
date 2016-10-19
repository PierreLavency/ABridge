<?php
// test integration with peristence


require_once("Model.php"); 
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName,1);

$x=new Model("Nohandler");
$r=$x->getModName() ;
	// logging result
	$xs = "$r=..->getModName() ;";
	$z->logLine ($xs);

$res=$x->save();
	// logging result
	$xs = "$res=x->save();";
	$z->logLine ($xs);

	
$r = $x-> getErrLog ();

$z->includeLog($r);
	
$z->save();
/*
$z->show();
*/
?>