<?php


use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Log\Logger;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\View\View;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';
require_once 'View_case_Xref.php';


$testRun = true;

$log=new Logger();

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


if ($testRun) {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'View_init_Xref_testRun');
} else {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'View_init_Xre');
}
