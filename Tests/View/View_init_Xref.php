<?php


use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Log\Logger;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\View\View;
use ABridge\ABridge\View\Vew;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';
require_once 'View_case_Xref.php';


$testRun = 0;

$log=new Logger();

/**************************************/

$classes = ['Dir'];
$baseTypes=['dataBase'];

$prm=UtilsC::genPrm($classes, 'View_Xref_Test', $baseTypes);

Mod::reset();
Mod::get()->init($prm['application'], $prm['handlers']);

$show = false;
$test = viewCasesXref();



for ($i=0; $i<count($test); $i++) {
    if ($show) {
        echo "<br>" ;
    };
    $id = $test[$i][0];
    $p = $test[$i][1];
    $s = $test[$i][2];
    
    Mod::get()->begin();
    
    $handle = new Handle($p, CstMode::V_S_READ, null);
    Vew::reset();
    $cname=$handle->getModName();
    $config = [
            $cname=> [
                    'attrList' => [
                            CstView::V_S_REF        => ['Name'],
                    ],
                    'attrHtml'=> [
                            CstMode::V_S_CREA => [
                                    'Mother'=>CstHTML::H_T_SELECT,
                            ]
                    ],
            ]
            
    ];
    Vew::get()->init([], $config);
    $v = new View($handle);
    $v->setTopMenu(['/dir']);
    $res = $v->show($s, false);
    $log->logLine($res);
    
    Mod::get()->End();
}



if ($testRun) {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'View_init_Xref_testRun');
} else {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'View_init_Xref');
}
