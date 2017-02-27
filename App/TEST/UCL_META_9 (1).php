<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");


// when running this data will be lost !!

	$ctrl = new Controler($config);
	$ctrl->beginTrans();

	$x = new Model('Student');	
	$x->addAttr('User',M_REF,'/User');
	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();

	$x = new Model('Cours');	
	$x->addAttr('User',M_REF,'/User');
	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();
	
	$x = new Model('Prof');	
	$x->addAttr('User',M_REF,'/User');
	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();
	
	$ctrl->commit();
	

?>	