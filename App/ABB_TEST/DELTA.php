<?php
	require_once 'CLASSDEC.php';


	$obj = new Model($Session);

	$res = $obj->addAttr('Password',		M_STRING);

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	

	