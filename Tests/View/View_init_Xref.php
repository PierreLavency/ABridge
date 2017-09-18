<?php

use ABridge\ABridge\UnitTest;

use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\View\View;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\View\CstHTML;

require_once 'View_case_Xref.php';

$logName = basename(__FILE__, ".php");

$log=new UnitTest('C:/Users/pierr/ABridge/Datastore/', $logName, 1);

/**************************************/

$classes = ['Dir'];
$baseTypes=['dataBase'];

$prm=UtilsC::genPrm($classes, 'View_Xref_Test', $baseTypes);

Mod::reset();
Mod::get()->init($prm['application'], $prm['handlers']);

$show = false;
$test = viewCasesXref();

Mod::get()->begin();

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
Mod::get()->End();

$log->saveTest();

//$log->showTest();
