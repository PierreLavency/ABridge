<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");


// when running this data will be lost !!

	$ctrl = new Controler($config);
	$ctrl->beginTrans();

/*	
	// Inscription - add combined key 
	
	$x = new Model('Inscription');
	
	$x->setCkey(['De','A'],true);
	
	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();

	// Student Credit d heure

	$x = new Model('Student');
	
	$x->addAttr('NbrCours',M_INT,M_P_EVAL);
	$x->addAttr('NbrCredits',M_INT,M_P_EVALP);
	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();

	// Cours Credit d heure et charge
	$x = new Model('Cours');

	$x->addAttr('Credits',M_INT);
	
	$x->addAttr('Par',M_CREF,'/Charge/De');

	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();

	// Profs
	$x=new Model('Prof');
	$x->addAttr('Name',M_STRING);
	$x->addAttr('SurName',M_STRING);
	$x->addAttr('BirthDay',M_DATE);


	$path='/Code/1/Values';
	$x->addAttr('Sexe',M_CODE,$path);

	$x->addAttr('Donne',M_CREF,'/Charge/Par');

	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();
	
	//Charge
	$x=new Model('Charge');
	$x->addAttr('De',M_REF,'/Cours');
	$x->addAttr('Par',M_REF,'/Prof');
	$x->setCkey(['De','Par'],true);

	$x->saveMod();	
	$r = $x-> getErrLog ();
	$r->show();
*/	
	$ctrl->commit();
	

?>	