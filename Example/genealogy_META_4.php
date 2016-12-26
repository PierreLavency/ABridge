<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");

// when running this data will be lost !!

	$db = getBaseHandler('fileBase','genealogy');
	initStateHandler('Student', 'fileBase','genealogy');
	$db->beginTrans();
	
	// Cours 

	$person = new Model('Student');
	
	$res = $person->addAttr('CreditNumber',M_INT,M_P_EVAL);
	$res = $person->saveMod();	
	$r = $person-> getErrLog ();
	$r->show();

	$db->commit();
	

?>	