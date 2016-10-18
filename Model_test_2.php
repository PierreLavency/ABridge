<?php
// test integration with type check


require_once("Model.php"); 
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);

$x=new Model("Typechecking");

$r=$x->getModName() ;
	// logging result
	$xs = "$r=..->getModName() ;";
	$z->logLine ($xs);

$res=$x->addAttr('a_id'	,M_ID);
	// logging result
	$xs = "$res=x->addAttr('a_id'	,M_ID);";
	$z->logLine ($xs);
$res=$x->addAttr('a_ref'	,M_REF);
	// logging result
	$xs = "$res=x->addAttr('a_ref'	,M_REF);";
	$z->logLine ($xs);
$res=$x->addAttr('a_string'	,M_STRING);
	// logging result
	$xs = "$res=x->addAttr('a_string'	,M_STRING);";
	$z->logLine ($xs);
$res=$x->addAttr('a_float'	,M_FLOAT);
	// logging result
	$xs = "$res=x->addAttr('a_float'	,M_FLOAT);";
	$z->logLine ($xs);
$res=$x->addAttr('a_alpha'	,M_ALPHA);
	// logging result
	$xs = "$res=x->addAttr('a_alpha'	,M_ALPHA);";
	$z->logLine ($xs);
$res=$x->addAttr('a_alnum'	,M_ALNUM);
	// logging result
	$xs = "$res=x->addAttr('a_alnum'	,M_ALNUM);";
	$z->logLine ($xs);

$res=$x->setVal('a_id','1');
	// logging result
	$xs = "$res=x->setVal('a_id','1');";
	$z->logLine ($xs);
$res=$x->setVal('a_id',1);
	// logging result
	$xs = "$res=x->setVal('a_id',1);";
	$z->logLine ($xs);
$res=$x->setVal('a_id',-1);
	// logging result
	$xs = "$res=x->setVal('a_id',-1);";
	$z->logLine ($xs);
$res=$x->setVal('a_id',0);
	// logging result
	$xs = "$res=x->setVal('a_id',0);";
	$z->logLine ($xs);
$res=$x->setVal('a_id','AB');
	// logging result
	$xs = "$res=x->setVal('a_id','AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_id','1AB');
	// logging result
	$xs = "$res=x->setVal('a_id','1AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_id','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_id','1AB_');";
	$z->logLine ($xs);
$res=$x->setVal('a_id','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_id','1A,B');";
	$z->logLine ($xs);
	
$res=$x->setVal('a_ref','1');
	// logging result
	$xs = "$res=x->setVal('a_ref','1');";
	$z->logLine ($xs);
$res=$x->setVal('a_ref',1);
	// logging result
	$xs = "$res=x->setVal('a_ref',1);";
	$z->logLine ($xs);
$res=$x->setVal('a_ref',-1);
	// logging result
	$xs = "$res=x->setVal('a_ref',-1);";
	$z->logLine ($xs);
$res=$x->setVal('a_ref',0);
	// logging result
	$xs = "$res=x->setVal('a_ref',0);";
	$z->logLine ($xs);
$res=$x->setVal('a_ref','AB');
	// logging result
	$xs = "$res=x->setVal('a_ref','AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_ref','1AB');
	// logging result
	$xs = "$res=x->setVal('a_ref','1AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_ref','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_ref','1AB_');";
	$z->logLine ($xs);
$res=$x->setVal('a_ref','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_ref','1A,B');";
	$z->logLine ($xs);
	
$res=$x->setVal('a_string','1');
	// logging result
	$xs = "$res=x->setVal('a_string','1');";
	$z->logLine ($xs);
$res=$x->setVal('a_string',1);
	// logging result
	$xs = "$res=x->setVal('a_string',1);";
	$z->logLine ($xs);
$res=$x->setVal('a_string',-1);
	// logging result
	$xs = "$res=x->setVal('a_string',-1);";
	$z->logLine ($xs);
$res=$x->setVal('a_string',0);
	// logging result
	$xs = "$res=x->setVal('a_string',0);";
	$z->logLine ($xs);
$res=$x->setVal('a_string','AB');
	// logging result
	$xs = "$res=x->setVal('a_string','AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_string','1AB');
	// logging result
	$xs = "$res=x->setVal('a_string','1AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_string','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_string','1AB_');";
	$z->logLine ($xs);
$res=$x->setVal('a_string','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_string','1A,B');";
	$z->logLine ($xs);
	
$res=$x->setVal('a_float','1');
	// logging result
	$xs = "$res=x->setVal('a_float','1');";
	$z->logLine ($xs);
$res=$x->setVal('a_float',1.5);
	// logging result
	$xs = "$res=x->setVal('a_float',1.5;";
	$z->logLine ($xs);
$res=$x->setVal('a_float',-1);
	// logging result
	$xs = "$res=x->setVal('a_float',-1);";
	$z->logLine ($xs);
$res=$x->setVal('a_float',0);
	// logging result
	$xs = "$res=x->setVal('a_float',0);";
	$z->logLine ($xs);
$res=$x->setVal('a_float','AB');
	// logging result
	$xs = "$res=x->setVal('a_float','AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_float','1AB');
	// logging result
	$xs = "$res=x->setVal('a_float','1AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_float','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_float','1AB_');";
	$z->logLine ($xs);
$res=$x->setVal('a_float','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_float','1A,B');";
	$z->logLine ($xs);

$res=$x->setVal('a_alpha','1');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1');";
	$z->logLine ($xs);
$res=$x->setVal('a_alpha',1);
	// logging result
	$xs = "$res=x->setVal('a_alpha',1);";
	$z->logLine ($xs);
$res=$x->setVal('a_alpha',-1);
	// logging result
	$xs = "$res=x->setVal('a_alpha',-1);";
	$z->logLine ($xs);
$res=$x->setVal('a_alpha',0);
	// logging result
	$xs = "$res=x->setVal('a_alpha',0);";
	$z->logLine ($xs);
$res=$x->setVal('a_alpha','AB');
	// logging result
	$xs = "$res=x->setVal('a_alpha','AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_alpha','1AB');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_alpha','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1AB_');";
	$z->logLine ($xs);
$res=$x->setVal('a_alpha','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_alpha','1A,B');";
	$z->logLine ($xs);

$res=$x->setVal('a_alnum','1');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1');";
	$z->logLine ($xs);
$res=$x->setVal('a_alnum',1);
	// logging result
	$xs = "$res=x->setVal('a_alnum',1);";
	$z->logLine ($xs);
$res=$x->setVal('a_alnum',-1);
	// logging result
	$xs = "$res=x->setVal('a_alnum',-1);";
	$z->logLine ($xs);
$res=$x->setVal('a_alnum',0);
	// logging result
	$xs = "$res=x->setVal('a_alnum',0);";
	$z->logLine ($xs);
$res=$x->setVal('a_alnum','AB');
	// logging result
	$xs = "$res=x->setVal('a_alnum','AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_alnum','1AB');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1AB');";
	$z->logLine ($xs);
$res=$x->setVal('a_alnum','1AB_');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1AB_');";
	$z->logLine ($xs);
$res=$x->setVal('a_alnum','1A,B"');
	// logging result
	$xs = "$res=x->setVal('a_alnum','1A,B');";
	$z->logLine ($xs);
	
$r = $x-> getErrLog ();
$z->includeLog($r);
	
$z->save();
/*
$z->show();
*/
?>