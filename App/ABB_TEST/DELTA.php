<?php
	require_once 'CLASSDEC.php';
	
	
	$obj = new Model($Session);
	$res= $obj->deleteMod();

	$res = $obj->addAttr($User,			M_REF,		'/'.$User);
	$res = $obj->addAttr($Role,			M_REF,		'/'.$Role);
	$res = $obj->addAttr('UserId',		M_STRING);	
	$res = $obj->addAttr('Password',	M_STRING, 	M_P_TEMP);	
	$res = $obj->addAttr('BKey',		M_STRING, 	M_P_EVALP);
	$res = $obj->addAttr('ValidStart',	M_INT, 		M_P_EVALP);
	$res = $obj->addAttr('ValidFlag',	M_INT, 		M_P_EVALP);
		
	$res = $obj->setBkey('BKey',true);
	$res = $obj->setMdtr('BKey',true);
	
	$res = $obj->setMdtr('ValidStart',true);
	$res = $obj->setMdtr('ValidFlag', true);
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
