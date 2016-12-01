<?php

require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* standalone test */');
	
/**************************************/

require_once("Model.php"); 
require_once("View.php"); 

$test1=[V_TYPE=>V_ELEM,V_ATTR => 'A1', V_PROP => V_P_LBL];
$test2=[V_TYPE=>V_ELEM,V_ATTR => 'A1', V_PROP => V_P_NAME];
$test3=[V_TYPE=>V_ELEM,V_ATTR => 'A1', V_PROP => V_P_VAL];
$test4=[V_TYPE=>V_ELEM,V_ATTR => 'A1', V_PROP => V_P_TYPE];
$test5=[V_TYPE=>V_ELEM,V_ATTR => 'A1', V_PROP=>V_P_VAL];
$test6=[V_TYPE=>V_LIST,V_ARG=>[$test1,$test2,$test3,$test4,$test5]];

$x=new Model('test');
$x->deleteMod();

$x->addAttr('A1',M_INT);
$x->addAttr('A2',M_INT);


$x->setVal('A1',1);

$x->setVal('A2',2);

$method = 'GET';

$v = new View($x);

$L=$x->getAllAttr();
$L= array_diff($L,['vnum','ctstp','utstp']);
$v->setAttrList($L);
$L = array('id'=>'object reference','vnum'=>'version number','ctstp'=>'creation time stamp',
'A1' => "Attribute1",H_T_SUBMIT=>"Confirmer");
$v->setLblList($L);
$v->setPropList([V_P_NAME,V_P_LBL,V_P_TYPE,V_P_VAL]);
$show=false;

$r = $v->subst($test1,V_S_READ);
$res = genFormElem($r,$show);
$line = "$res = genFormElem(r,$show)";
$log->logLine ($line);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test2,V_S_READ);
$res = genFormElem($r,$show);
	// logging result
$line = "$res = genFormElem(r,$show)";
$log->logLine ($line);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test3,V_S_READ);
$res = genFormElem($r,$show);
	// logging result
$line = "$res = genFormElem(r,$show)";
$log->logLine ($line);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test4,V_S_READ);
$res = genFormElem($r,$show);
	// logging result
$line = "$res = genFormElem(r,$show)";
$log->logLine ($line);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test5,V_S_UPDT);
$res = genFormElem($r,$show);
	// logging result
$line = "$res = genFormElem(r,$show)";
$log->logLine ($line);
	// end
if ($show) {echo "<br>" ; };

$r = $v->subst($test6,V_S_READ);
$res = genFormElem($r,$show);
	// logging result
$line = "$res = genFormElem(r,$show)"; $log->logLine ($line);
	// end
if ($show) {echo "<br>" ; };

$res = $v->show(V_S_READ,$show);
$line = "$res = v->show(V_S_READ,$show);"; $log->logLine ($line);

if ($show) {echo "<br>" ; };
$res = $v->show(V_S_CREA,$show);
$line = "$res = v->show(V_S_CREA,$show);es = v->show('V_S_CREA,$show);"; $log->logLine ($line);

if ($show) {echo "<br>" ; };

$v->setAttrList(['A1','A2','id'],V_S_REF);

$res = $v->show(V_S_REF,$show);
$line = "$res = v->show(V_S_LABL,$show);"; $log->logLine ($line);

$log->saveTest();

//$log->showTest();


?>