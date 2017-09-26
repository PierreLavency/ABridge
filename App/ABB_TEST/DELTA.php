<?php
	require_once 'CLASSDEC.php';
	require_once 'UtilsC.php'; 

	$bindings = [$Role=>$Role,$User=>$User,$Distribution=>$Distribution,$Session=>$Session];
	
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
    $res = $obj->setProp('Name',Model::P_BKY);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	
	

