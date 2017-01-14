<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

// when running this data will be lost !!

	$db = getBaseHandler('dataBase','abb');
	initStateHandler('ACode', 'dataBase','abb');
	initStateHandler('CType', 'dataBase','abb');

	
	$db->beginTrans();
	
	// Abstract 
	
	$obj = new Model('ACode');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Value',M_STRING);
	$res = $obj->setMdtr('Value',true);
    $res = $obj->setBkey('Value',true);	
	$res = $obj->setAbstr();
 	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

	
	// Ctype
	
	$obj = new Model('CType');
	$res= $obj->deleteMod();

	$res = $obj->setInhNme('ACode');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	
	$db->commit();
	
