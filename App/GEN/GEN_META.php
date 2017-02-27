<?php
	
require_once('controler.php');
require_once("GEN_SETUP.php");

// when running this data will be lost !!

	$ctrl = new Controler($config);
	$ctrl->beginTrans();
	
	// CodeVal
	$Code = 'Code';	
	$CodeVal= 'CodeValue';
	
	$codeval = new Model($CodeVal);
	$res= $codeval->deleteMod();
	
	$res = $codeval->addAttr('Name',M_STRING);

	$path='/'.$Code;
	$res = $codeval->addAttr('ValueOf',M_REF,$path);
	$res=$codeval->setMdtr('ValueOf',true); // Mdtr
		
	$res = $codeval->saveMod();	

	$r = $codeval-> getErrLog ();
	$r->show();
	
	// Code
		
	$code = new Model($Code);
	$res= $code->deleteMod();
		
	$res = $code->addAttr('Name',M_STRING); 
	$res=$code->setBkey('Name',true);// Unique
		
	$path='/'.$CodeVal.'/ValueOf';
	$res = $code->addAttr('Values',M_CREF,$path);
		
	$res = $code->saveMod();
	
	$r = $code-> getErrLog ();
	$r->show();

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
	
	$res= $person->addAttr('User',M_REF,'/User');	
	
	$res = $person->saveMod();	
	$r = $person->getErrLog ();
	$r->show();

	
	// User 
	
	$obj = new Model('User');
	$res= $obj->deleteMod();
	$res = $obj->addAttr('Code',M_STRING);
	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('SurName',M_STRING);
	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	$ctrl->commit();
	
