<?php

use ABridge\ABridge\UnitTest;

use ABridge\ABridge\Handler;

use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;


use ABridge\ABridge\View\View;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\View\CstHTML;

require_once 'View_case_Xref.php';

$logName = basename(__FILE__, ".php");

$log=new UnitTest($logName, 1);

/**************************************/


$show = false;
$test = viewCasesXref();

$db = handler::get()->getBase('dataBase', 'test');
handler::get()->setStateHandler('dir', 'dataBase', 'test');

$db->beginTrans();

for ($i=0; $i<count($test); $i++) {
    if ($show) {
        echo "<br>" ;
    };
    $id = $test[$i][0];
    $p = $test[$i][1];
    $s = $test[$i][2];

    $home= null;
    $request = new Request($p, CstMode::V_S_READ);
    $handle = new Handle($p, CstMode::V_S_READ, $home);
    $v = new View($handle);

    $v->setTopMenu(['/dir']);
    $v->setAttrListHtml(['Mother'=>CstHTML::H_T_SELECT], CstMode::V_S_CREA);
    $v->setAttrList(['Name'], CstView::V_S_REF);
    $res = $v->show($s, $show);
    $log->logLine($res);
}

$log->saveTest();

//$log->showTest();
