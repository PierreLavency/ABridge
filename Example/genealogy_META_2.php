<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");

// when running this data will be lost !!

	$fb->beginTrans();
	$db->beginTrans();
	
	// Cours 

	$person = new Model($Person);
//	$res = $person->delAttr('text',M_TXT);
	
    $res = $person->addAttr('text',M_TXT);
	$res = $person->saveMod();	
	$r = $person-> getErrLog ();
	$r->show();
	$fb->commit();	
	$db->commit();
	

?>	