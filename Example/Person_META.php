<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");

// when running this data will be lost !!

	$db = getBaseHandler('dataBase','genealogy');
	initStateHandler('Person', 'dataBase','genealogy');
	$db->beginTrans();
	
	// Person
	
	$person = new Model('Person');
	$res= $person->deleteMod();

	$res = $person->addAttr('Name',M_STRING);
	$res = $person->setDflt('Name','Lavency'); // HERE
	$res = $person->addAttr('SurName',M_STRING);
	$res = $person->addAttr('BirthDay',M_DATE);
	
	$path='/Code/1/Values';
	$res = $person->addAttr('Sexe',M_CODE,$path);	
	
	$path='/Code/2/Values';
	$res = $person->addAttr('Country',M_CODE,$path);	


	$res = $person->addAttr('Father',M_REF,'/Person');
	$res = $person->addAttr('Mother',M_REF,'/Person');

	$path='/Person/Father';
	$res = $person->addAttr('FatherOf',M_CREF,$path);	

	$path='/Person/Mother';
	$res = $person->addAttr('MotherOf',M_CREF,$path);

    $res = $person->addAttr('DeathDate',M_DATE);
	$res = $person->addAttr('Age',M_INT,M_P_EVAL);
	$res = $person->addAttr('DeathAge',M_INT,M_P_EVALP);	

    $res = $person->addAttr('text',M_TXT);
	
	
	$res = $person->saveMod();	
	$r = $person->getErrLog ();
	$r->show();

	$db->commit();
	
