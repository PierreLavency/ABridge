<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

// when running this data will be lost !!

	$db = getBaseHandler('dataBase','abb');
	initStateHandler('ABB', 'dataBase','abb');
	initStateHandler('Interface', 'dataBase','abb');
	initStateHandler('Exchange', 'dataBase','abb');
	
	$db->beginTrans();
	
	// Architecture building block 
	
	$obj = new Model('ABB');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('In',M_CREF,'/Interface/Of');
	$res = $obj->addAttr('Out',M_CREF,'/Exchange/OutOf');
	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

	// Interface

	$obj = new Model('Interface');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('Of',M_REF,'/ABB');
	$res = $obj->addAttr('UsedBy',M_CREF,'/Exchange/Through');
		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	

	// Exchange
	
	$obj = new Model('Exchange');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('Through',M_REF,'/Interface');
	$res = $obj->addAttr('OutOf',M_REF,'/ABB');
	$obj->setCkey(['OutOf','Through'],true);	
	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	
	
	$db->commit();
	
