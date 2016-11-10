<?php


require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);


$log->logLine('/* standalone test */');
	
/**************************************/

require_once("Model.php"); 

try {$x=new Model(1);} catch (Exception $e) {$r= 'Exception reçue : '. $e->getMessage();}
										// logging result
										$xs = "try {..=new Model(1);} catch  $r";
										$log->logLine ($xs);
$x=new Model('Test');
$r=$x->getModName();
										// logging result
										$xs = "$r=..->getModName();";
										$log->logLine ($xs);

$r=$x->getVal('id');
										// logging result
										$xs = "$r=..->getVal('id');";
										$log->logLine ($xs);
$r=$x->addAttr('y');
										// logging result
										$xs = "$r=x->addAttr('y');";
										$log->logLine ($xs);

$r=$x->delAttr('y');
										// logging result
										$xs = "$r=x->delAttr('y')";
										$log->logLine ($xs);
$r=$x->addAttr('a1',M_INT);
										// logging result
										$xs = "$r=x->addAttr('a1',M_INT);";
										$log->logLine ($xs);
$r=$x->setVal("a1",1);
										// logging result
										$xs = "$r=..->setVal(a1,1);";
										$log->logLine ($xs);

$r=$x->existsAttr("id") ;
										// logging result
										$xs = "$r=..->existsAttr(id) ;";
										$log->logLine ($xs);
$r=$x->existsAttr("a1") ;
										// logging result
										$xs = "$r=..->existsAttr(a1) ;";
										$log->logLine ($xs);
$r=$x->existsAttr("a2") ;
										// logging result
										$xs = "$r=..->existsAttr(a2) ;";
										$log->logLine ($xs);
$r=$x->setVal("a1",2);
										// logging result
										$xs = "$r=..->setVal(a1,2);";
										$log->logLine ($xs);
$r=$x->setVal("id",2);
										// logging result
										$xs = "$r=..->setVal(id,2);";
										$log->logLine ($xs);
$r=$x->getVal("a1")  ;
										// logging result
										$xs = "$r=..->getVal(a1)  ;";
										$log->logLine ($xs);
$r=$x->getVal("id")  ;
										// logging result
										$xs = "$r=..->getVal(id)  ;";
										$log->logLine ($xs);
$r=$x->setVal("a2",3);
										// logging result
										$xs = "$r=..->setVal(a2,3);";
										$log->logLine ($xs);
$r=$x->addAttr('a2',M_INT);
										// logging result
										$xs = "$r=x->addAttr('a2',M_INT);";
										$log->logLine ($xs);
$r=$x->setVal('a2',3);
										// logging result
										$xs = "$r=..->setVal('a2',3);";
										$log->logLine ($xs);
$r= implode (' , ',$x->getAllAttr());
										// logging result
										$xs = "$r= implode (' , ',x->getAllAttr());";
										$log->logLine ($xs);

$log->saveTest();

//$log->showTest();

?>