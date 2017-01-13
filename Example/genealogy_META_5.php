<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");


	$db = getBaseHandler('dataBase','genealogy');
	initStateHandler('Person', 'dataBase','genealogy');
	$db->beginTrans();

	
	$x = new Model('Person');
	$x->addAttr('DeathAge',M_INT,M_P_EVALP);	
	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();

	$db->commit();
	
