<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");

// when running this data will be lost !!

	$db = getBaseHandler('dataBase','genealogy');
	initStateHandler('Person', 'dataBase','genealogy');
	$db->beginTrans();
	
	// Cours 

	$person = new Model('Person');
	
	$res = $person->delAttr('DeathDate');
	$res = $person->delAttr('Age');
    $res = $person->addAttr('DeathDate',M_DATE);
	$res = $person->addAttr('Age',M_INT,M_P_EVAL);
	$res = $person->saveMod();	
	$r = $person-> getErrLog ();
	$r->show();

	$db->commit();
	

?>	