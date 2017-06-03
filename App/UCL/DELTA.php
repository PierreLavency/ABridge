<?php
	
	require_once 'CLASSDEC.php';
	
	$obj = new Model($Session);
	$res= $obj->deleteMod();
	
	$obj->initMod();
		
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";