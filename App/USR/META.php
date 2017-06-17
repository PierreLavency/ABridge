<?php
	
// when running this data will be lost !!

	require_once 'CLASSDEC.php';
	require_once 'UtilsC.php'; 
	
/*******************************  User  ************************/

	$bindings = [$Session=>$Session,$User=>$User,$Role=>$Role,$Distribution=>$Distribution];	
	UtilsC::createMods($bindings);	
	
	// User	
	$obj = new Model($User);
	
	$res = $obj->addAttr($Group,M_REF,'/'.$Group);	
	
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Group
	
	$obj = new Model($Group);
	$res= $obj->deleteMod();

 	$res = $obj->addAttr('Name',		M_STRING);
	$res = $obj->addAttr('Users',		M_CREF,'/'.$User.'/'.$Group);
    $res = $obj->setBkey('Name',true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	