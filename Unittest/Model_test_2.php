<?php


require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* integration with type check */');
	
/**************************************/

require_once("Model.php"); 

$x=new Model("Typechecking");

$r=$x->getModName() ;
	// logging result
	$xs = "$r=..->getModName() ;";
	$log->logLine ($xs);

$res=$x->addAttr('a_id'	,M_ID);
	// logging result
	$xs = "$res=x->addAttr('a_id'	,M_ID);";
	$log->logLine ($xs);
$res=$x->addAttr('a_string'	,M_STRING);
	// logging result
	$xs = "$res=x->addAttr('a_string'	,M_STRING);";
	$log->logLine ($xs);
$res=$x->addAttr('a_float'	,M_FLOAT);
	// logging result
	$xs = "$res=x->addAttr('a_float'	,M_FLOAT);";
	$log->logLine ($xs);
$res=$x->addAttr('a_alpha'	,M_ALPHA);
	// logging result
	$xs = "$res=x->addAttr('a_alpha'	,M_ALPHA);";
	$log->logLine ($xs);
$res=$x->addAttr('a_alnum'	,M_ALNUM);
	// logging result
	$xs = "$res=x->addAttr('a_alnum'	,M_ALNUM);";
	$log->logLine ($xs);

$res=$x->setVal('a_id','1');
	// logging result
	$xs = "$res=x->setVal('a_id','1');";
	$log->logLine ($xs);
$res=$x->setVal('a_id',1);
	// logging result
	$xs = "$res=x->setVal('a_id',1);";
	$log->logLine ($xs);
$res=$x->setVal('a_id',-1);
	// logging result
	$xs = "$res=x->setVal('a_id',-1);";
	$log->logLine ($xs);
$res=$x->setVal('a_id',0);
	// logging result
	$xs = "$res=x->setVal('a_id',0);";
	$log->logLine ($xs);
$res=$x->setVal('a_id','AB');
	// logging result
	$xs = "$res=x->setVal('a_id','AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_id','1AB');
	// logging result
	$xs = "$res=x->setVal('a_id','1AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_id','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_id','1AB_');";
	$log->logLine ($xs);
$res=$x->setVal('a_id','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_id','1A,B');";
	$log->logLine ($xs);
	
	
$res=$x->setVal('a_string','1');
	// logging result
	$xs = "$res=x->setVal('a_string','1');";
	$log->logLine ($xs);
$res=$x->setVal('a_string',1);
	// logging result
	$xs = "$res=x->setVal('a_string',1);";
	$log->logLine ($xs);
$res=$x->setVal('a_string',-1);
	// logging result
	$xs = "$res=x->setVal('a_string',-1);";
	$log->logLine ($xs);
$res=$x->setVal('a_string',0);
	// logging result
	$xs = "$res=x->setVal('a_string',0);";
	$log->logLine ($xs);
$res=$x->setVal('a_string','AB');
	// logging result
	$xs = "$res=x->setVal('a_string','AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_string','1AB');
	// logging result
	$xs = "$res=x->setVal('a_string','1AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_string','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_string','1AB_');";
	$log->logLine ($xs);
$res=$x->setVal('a_string','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_string','1A,B');";
	$log->logLine ($xs);
	
$res=$x->setVal('a_float','1');
	// logging result
	$xs = "$res=x->setVal('a_float','1');";
	$log->logLine ($xs);
$res=$x->setVal('a_float',1.5);
	// logging result
	$xs = "$res=x->setVal('a_float',1.5;";
	$log->logLine ($xs);
$res=$x->setVal('a_float',-1);
	// logging result
	$xs = "$res=x->setVal('a_float',-1);";
	$log->logLine ($xs);
$res=$x->setVal('a_float',0);
	// logging result
	$xs = "$res=x->setVal('a_float',0);";
	$log->logLine ($xs);
$res=$x->setVal('a_float','AB');
	// logging result
	$xs = "$res=x->setVal('a_float','AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_float','1AB');
	// logging result
	$xs = "$res=x->setVal('a_float','1AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_float','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_float','1AB_');";
	$log->logLine ($xs);
$res=$x->setVal('a_float','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_float','1A,B');";
	$log->logLine ($xs);

$res=$x->setVal('a_alpha','1');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1');";
	$log->logLine ($xs);
$res=$x->setVal('a_alpha',1);
	// logging result
	$xs = "$res=x->setVal('a_alpha',1);";
	$log->logLine ($xs);
$res=$x->setVal('a_alpha',-1);
	// logging result
	$xs = "$res=x->setVal('a_alpha',-1);";
	$log->logLine ($xs);
$res=$x->setVal('a_alpha',0);
	// logging result
	$xs = "$res=x->setVal('a_alpha',0);";
	$log->logLine ($xs);
$res=$x->setVal('a_alpha','AB');
	// logging result
	$xs = "$res=x->setVal('a_alpha','AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_alpha','1AB');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_alpha','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1AB_');";
	$log->logLine ($xs);
$res=$x->setVal('a_alpha','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1A,B');";
	$log->logLine ($xs);

$res=$x->setVal('a_alnum','1');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1');";
	$log->logLine ($xs);
$res=$x->setVal('a_alnum',1);
	// logging result
	$xs = "$res=x->setVal('a_alnum',1);";
	$log->logLine ($xs);
$res=$x->setVal('a_alnum',-1);
	// logging result
	$xs = "$res=x->setVal('a_alnum',-1);";
	$log->logLine ($xs);
$res=$x->setVal('a_alnum',0);
	// logging result
	$xs = "$res=x->setVal('a_alnum',0);";
	$log->logLine ($xs);
$res=$x->setVal('a_alnum','AB');
	// logging result
	$xs = "$res=x->setVal('a_alnum','AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_alnum','1AB');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1AB');";
	$log->logLine ($xs);
$res=$x->setVal('a_alnum','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1AB_');";
	$log->logLine ($xs);
$res=$x->setVal('a_alnum','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1A,B');";
	$log->logLine ($xs);
	
$r = $x-> getErrLog ();
$log->includeLog($r);
	
$log->saveTest();

//$log->showTest();

?>