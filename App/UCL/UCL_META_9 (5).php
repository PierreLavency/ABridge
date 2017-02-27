<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");


// when running this data will be lost !!

	$ctrl = new Controler($config);
	$ctrl->beginTrans();
	
	
	$obj = new Model('User');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('SurName',M_STRING);
		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

	$obj = new Model('Session');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('User',M_REF,'/User');
 	$res = $obj->addAttr('Comment',M_STRING);
		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	

	$ctrl->commit();
	
