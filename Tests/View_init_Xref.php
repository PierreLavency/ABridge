<?php
require_once('Path.php');
require_once("UnitTest.php");
require_once('View_case_Xref.php');

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

/**************************************/

require_once("Model.php"); 
require_once("View.php"); 


$show = false;
$test = viewCasesXref();

$db = getBaseHandler('dataBase','test');
initStateHandler('dir', 'dataBase','test');

$db->beginTrans();

for ($i=0;$i<count($test);$i++) {
if ($show) {echo "<br>" ; };
$id = $test[$i][0];
$p = $test[$i][1];
$s = $test[$i][2];
$y =  new Model('dir',$id);
$path = new Path($p);
$v = new View($y);
$v->setNavClass(['dir']);	
$v->setAttrList(['Name'],V_S_REF);	
$res = $v->show($path,$s,$show); 
$log->logLine($res);
}

$log->saveTest();

//$log->showTest();


