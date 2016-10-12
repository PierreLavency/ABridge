<?php

require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);

// test run 

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

$r = $x->logsize();
										// logging result;
										$xs = "$r = ...->logsize();";
										$z->logLine ($xs);
$x->show();


$z->save();


?>