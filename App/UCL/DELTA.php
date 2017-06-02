<?php
	
	require_once 'CLASSDEC.php';
	
	$obj = new Model($Role);

    $res = $obj->setBkey('Name',true);	

	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";