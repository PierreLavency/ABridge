<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

// when running this data will be lost !!

	$db = getBaseHandler('dataBase','abbtest');
	initStateHandler('User', 'dataBase','abbtest');

	
	$db->beginTrans();
	
	// Abstract 
	
	$obj = new Model('User');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('SurName',M_STRING);
		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();


	$db->commit();
	
