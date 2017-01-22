<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");


	$db = getBaseHandler('dataBase','genealogy');
	initStateHandler('Person', 'dataBase','genealogy');
	initStateHandler('User', 'dataBase','genealogy');
		
	$db->beginTrans();

/*
	$x = new Model('Person');
	$x->addAttr('User',M_REF,'/User');	
	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();
*/
	$obj = new Model('User');

	$res = $obj->addAttr('Person',M_CREF,'/Person/User');
		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	
	$db->commit();
	
