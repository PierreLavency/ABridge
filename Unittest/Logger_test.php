
<?php

require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$logName =$logName.'_test';

$log->logLine('/* standalone test */');
	
/**************************************/


// test run 

$x = new Logger($logName);

$s = "this is my first logged line";
$r=$x->logLine ($s);
										// logging result
										$xs = "$r=... logLine ($s);";
										$log->logLine ($xs);
$s ="this is my second logged line";
$r=$x->logLine ($s);
$c = $r+1;
										// logging result
										$xs = "$r=... logLine ($s);";
										$log->logLine ($xs);
$r=$x->save();
										// logging result
										$xs = "$r=...save();";
										$log->logLine ($xs);

$x = new Logger($logName);


$s = "$c lines logged";
$r=$x->logLine ($s);
										// logging result;
										$xs = "$r=...logLine ($s);";
										$log->logLine ($xs);
$r= $x->save();
										// logging result;
										$xs = "$r= ...save();";
										$log->logLine ($xs);

$x = new Logger($logName);

$r = $x->load();
										// logging result;
										$xs = "$r = ...load();";
										$log->logLine ($xs);

$r = $x->logsize();
										// logging result;
										$xs = "$r = ...->logsize();";
										$log->logLine ($xs);


$log->saveTest();

//$log->showTest();


?>