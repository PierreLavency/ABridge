
<?php
	require_once("UnitTest.php");
	$logName = basename(__FILE__, ".php");
	$x=new unitTest($logName);
	
	$r = $x->setVerbatim (1);
									$line = "$r = ...setVerbatim (1);";
									$x->logLine($line);
	
	$x->save();
	
?>