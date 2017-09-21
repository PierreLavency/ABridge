<?php

use ABridge\ABridge\UnitTest;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\GenJason;
use ABridge\ABridge\UtilsC;

require_once("GenJASON_case.php");

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';
$logName = basename(__FILE__, ".php");

$log=new UnitTest('C:/Users/pierr/ABridge/Datastore/', $logName, 0);

/**************************************/
$classes = ['testDir','testFile','CodeVal','Code'];
$baseTypes=['dataBase'];

$prm=UtilsC::genPrm($classes, 'GENJASON_Test', $baseTypes);

Mod::reset();
Mod::get()->init($prm['application'], $prm['handlers']);


$show = false;
$test = GenJasonCases();

Mod::get()->begin();

for ($i=0; $i<count($test); $i++) {
    if ($show) {
        echo "<br/>\n";
    }
    $case = $test[$i];
    $h= new Model($case[0], $case[1]);
    $r=GenJASON::genJASON($h, $show, false, $case[2]);
    $log->logLine($r);
}

Mod::get()->end();

$log->saveTest();

$log->showTest();
