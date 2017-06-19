<?php

require_once("UnitTest.php");
require_once 'Src/View/Tests/View_case_Xref.php';

$logName = basename(__FILE__, ".php");

$log=new UnitTest($logName, 1);

/**************************************/

require_once("Model.php");
require_once 'Src/View/Src/View.php';


$show = false;
$test = viewCasesXref();

$db = getBaseHandler('dataBase', 'test');
initStateHandler('dir', 'dataBase', 'test');

$db->beginTrans();

for ($i=0; $i<count($test); $i++) {
    if ($show) {
        echo "<br>" ;
    };
    $id = $test[$i][0];
    $p = $test[$i][1];
    $s = $test[$i][2];

    $home= null;
    $request = new Request($p, V_S_READ);
    $handle = new Handle($p, V_S_READ, $home);
    $v = new View($handle);

    $v->setTopMenu(['/dir']);
    $v->setAttrListHtml(['Mother'=>H_T_SELECT], V_S_CREA);
    $v->setAttrList(['Name'], V_S_REF);
    $res = $v->show($s, $show);
    $log->logLine($res);
}

$log->saveTest();

//$log->showTest();
