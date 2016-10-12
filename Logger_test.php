<?php

require_once("Logger.php");

$logName = "Logger_test";
$z = new Logger($logName."_run");


$x = new Logger("test");

$s = "this is my first logged line";
$r=$x->logLine ($s);
										// logging result
										$xs = "$r=... logLine ($s);";
										$z->logLine ($xs);
$s ="this is my second logged line";
$r=$x->logLine ($s);
$c = $r+1;
										// logging result
										$xs = "$r=... logLine ($s);";
										$z->logLine ($xs);
$x->show();
$r=$x->save();
										// logging result
										$xs = "$r=...save();";
										$z->logLine ($xs);

$x = new Logger();


$s = "$c lines logged";
$r=$x->logLine ($s);
										// logging result;
										$xs = "$r=...logLine ($s);";
										$z->logLine ($xs);
$x->show();
$r= $x->save();
										// logging result;
										$xs = "$r= ...save();";
										$z->logLine ($xs);

$x = new Logger("test");

$r = $x->load();
										// logging result;
										$xs = "$r = ...load();";
										$z->logLine ($xs);
$x->show();

$z->show();
$z->save();

$pz=new Logger($logName);
$pz->load();
$pz->show();

$r = $pz->diff($z);
echo "test resul  $logName :";
if ($r) {echo "fails diff in line $r";}
else {echo " ok";};

?>