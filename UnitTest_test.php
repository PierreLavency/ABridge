
<?php
	require_once("UnitTest.php");
	$logName = basename(__FILE__, ".php");
	$x=new unitTest($logName);
	
	$r = $x->setVerbatim (2);
									$line = "$r = ...setVerbatim (2);";
									$x->logLine($line);
	
	$x->save();
	
?>