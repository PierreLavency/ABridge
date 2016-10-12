<?php

require_once("Model.php"); 
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);

$x=new Model();

$r=$x->getModName() ;
										// logging result
										$xs = "$r=..->getModName() ;";
										$z->logLine ($xs);

try {$x=new Model(1);} catch (Exception $e) {$r= 'Exception reçue : '. $e->getMessage();}
										// logging result
										$xs = "try {..=new Model(1);} catch  $r";
										$z->logLine ($xs);
$x=new Model('Test');
$r=$x->getModName();
										// logging result
										$xs = "$r=..->getModName();";
										$z->logLine ($xs);

$r=$x->getVal('id');
										// logging result
										$xs = "$r=..->getVal('id');";
										$z->logLine ($xs);
$r=$x->setAttrList(["y"]);
										// logging result
										$xs = "$r=..->setAttrList([y]);";
										$z->logLine ($xs);

$r=$x->setAttrList(["a1"]);
										// logging result
										$xs = "$r=..->setAttrList([a1]);";
										$z->logLine ($xs);
$r=$x->setVal("a1",1);
										// logging result
										$xs = "$r=..->setVal(a1,1);";
										$z->logLine ($xs);

$r=$x->existsAttr("id") ;
										// logging result
										$xs = "$r=..->existsAttr(id) ;";
										$z->logLine ($xs);
$r=$x->existsAttr("a1") ;
										// logging result
										$xs = "$r=..->existsAttr(a1) ;";
										$z->logLine ($xs);
$r=$x->existsAttr("a2") ;
										// logging result
										$xs = "$r=..->existsAttr(a2) ;";
										$z->logLine ($xs);
$r=$x->setVal("a1",2);
										// logging result
										$xs = "$r=..->setVal(a1,2);";
										$z->logLine ($xs);
$r=$x->setVal("id",2);
										// logging result
										$xs = "$r=..->setVal(id,2);";
										$z->logLine ($xs);
$r=$x->getVal("a1")  ;
										// logging result
										$xs = "$r=..->getVal(a1)  ;";
										$z->logLine ($xs);
$r=$x->getVal("id")  ;
										// logging result
										$xs = "$r=..->getVal(id)  ;";
										$z->logLine ($xs);
$r=$x->setVal("a2",3);
										// logging result
										$xs = "$r=..->setVal(a2,3);";
										$z->logLine ($xs);
$r=$x->setAttrList(["a1","a2"]);
										// logging result
										$xs = "$r=..->setAttrList([a1,a2]);";
										$z->logLine ($xs);
$r=$x->setVal('a2',3);
										// logging result
										$xs = "$r=..->setVal('a2',3);";
										$z->logLine ($xs);
$r= implode (' , ',$x->getAttrList());
										// logging result
										$xs = "$r= implode (' , ',$..->getAttrList());";
										$z->logLine ($xs);

$z->save();
?>