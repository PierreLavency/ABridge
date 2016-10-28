<?php

require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* standalone test */');
	
/**************************************/

require_once("Model.php"); 
require_once("View.php"); 

$test1=[V_VIEW =>[V_ATTR => 'A1', V_PROP => V_P_LBL]];
$test2=[V_VIEW =>[V_ATTR => 'A1', V_PROP => V_P_NAME]];
$test3=[V_VIEW =>[V_ATTR => 'A1', V_PROP => V_P_VAL]];
$test4=[V_VIEW =>[V_ATTR => 'A1', V_PROP => V_P_TYPE]];
$test5=[H_TYPE=>H_T_TEXT,V_VIEW =>[V_ATTR => 'A1', V_PROP => V_P_INP]];
$test6=[H_TYPE=>H_T_LIST,H_ARG=>[$test1,$test2,$test3,$test4,$test5]];

$x=new Model('test');

$x->addAttr('A1');
$x->addAttr('A2');

$x->setTyp('A1',M_INT);
$x->setVal('A1',1);

$x->setTyp('A2',M_INT);
$x->setVal('A2',1);

$v = new View($x);

$v->attr_lbl = array('id'=>'object reference','vnum'=>'version number','ctstp'=>'creation time stamp','A1' => "Attribute1");


$show=false;

$r = $v->subst($test1);
$res = genFormElem($r,$show);
	// logging result
$xs = "$res = genFormElem(r,$show)";
$log->logLine ($xs);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test2);
$res = genFormElem($r,$show);
	// logging result
$xs = "$res = genFormElem(r,$show)";
$log->logLine ($xs);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test3);
$res = genFormElem($r,$show);
	// logging result
$xs = "$res = genFormElem(r,$show)";
$log->logLine ($xs);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test4);
$res = genFormElem($r,$show);
	// logging result
$xs = "$res = genFormElem(r,$show)";
$log->logLine ($xs);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test5);
$res = genFormElem($r,$show);
	// logging result
$xs = "$res = genFormElem(r,$show)";
$log->logLine ($xs);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test6);
$res = genFormElem($r,$show);
	// logging result
$xs = "$res = genFormElem(r,$show)";
$log->logLine ($xs);
	// end
if ($show) {echo "<br>" ; };

$r=$v->setSpec($test6);
$res = $v->show($show);
	// logging result
$xs = "$res = v->show($show);";
$log->logLine ($xs);
	// end
if ($show) {echo "<br>" ; };


$res = $v->showDefault($show);
 // not logged since date!!
 
if ($show) {echo "<br>" ; };

//$log->setVerbatim (2);
$log->saveTest();

//$log->showTest();


?>