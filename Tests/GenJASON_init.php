<?php

use ABridge\ABridge\Log\Logger;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\GenJason;
use ABridge\ABridge\UtilsC;

require_once("GenJASON_case.php");

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';

$testRun = false;

$log=new Logger();

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


if ($testRun) {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'GenJASON_init_Xref_testRun');
} else {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'GenJASON_init_Xre');
}
