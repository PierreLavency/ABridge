<?php

use ABridge\ABridge\View\GenHTML;
use ABridge\ABridge\Log\Logger;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';
require_once 'GenHTML_case.php';

$testRun = false;

$log=new Logger();


/**************************************/

$show = false;
$test = GenHTLMCases();

for ($i=0; $i<count($test); $i++) {
    $show = false;
    if ($show) {
        echo "<br/>\n";
    }
    $case = $test[$i];
    $r=GenHTML::genFormElem($case[0], $show);
    $log->logLine($r);
}

if ($testRun) {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'GenHTML_init_testRun');
} else {
    $log->save('C:/Users/pierr/ABridge/Datastore/', 'GenHTML_init');
}
