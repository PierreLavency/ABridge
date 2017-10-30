<?php
	
// when running this data will be lost !!
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

	// CodeVal
	$Code = 'Code';	
	$CodeVal= 'CodeValue';
	
	$codeval = new Model($CodeVal);
	$res= $codeval->deleteMod();
	
	$res = $codeval->addAttr('Name',Mtype::M_STRING);

	$path='/'.$Code;
	$res = $codeval->addAttr('ValueOf',Mtype::M_REF,$path);
	$res=$codeval->setProp('ValueOf', Model::P_MDT); 
		
	$res = $codeval->saveMod();	

	$r = $codeval-> getErrLog ();
	$r->show();
	
	// Code
		
	$code = new Model($Code);
	$res= $code->deleteMod();
		
	$res = $code->addAttr('Name',Mtype::M_STRING); 
	$res=$code->setProp('Name',Model::P_BKY);// Unique
		
	$path='/'.$CodeVal.'/ValueOf';
	$res = $code->addAttr('Values',Mtype::M_CREF,$path);
		
	$res = $code->saveMod();
	
	$r = $code-> getErrLog ();
	$r->show();

	
	// Person
	
	$person = new Model('Person');
	$res= $person->deleteMod();

	$res = $person->addAttr('Name',Mtype::M_STRING);
	$res = $person->setDflt('Name','Lavency'); // HERE
	$res = $person->addAttr('SurName',Mtype::M_STRING);
	$res = $person->addAttr('BirthDay',Mtype::M_DATE);
	
	$path='/Code/1/Values';
	$res = $person->addAttr('Sexe',Mtype::M_CODE,$path);	
	
	$path='/Code/2/Values';
	$res = $person->addAttr('Country',Mtype::M_CODE,$path);	


	$res = $person->addAttr('Father',Mtype::M_REF,'/Person');
	$res = $person->addAttr('Mother',Mtype::M_REF,'/Person');

	$path='/Person/Father';
	$res = $person->addAttr('FatherOf',Mtype::M_CREF,$path);	

	$path='/Person/Mother';
	$res = $person->addAttr('MotherOf',Mtype::M_CREF,$path);

    $res = $person->addAttr('DeathDate',Mtype::M_DATE);
	$res = $person->addAttr('Age',Mtype::M_INT);
	$res = $person->setProp('Age', Model::P_EVL);
	$res = $person->setProp('Age', Model::P_TMP);
	
	$res = $person->addAttr('DeathAge',Mtype::M_INT);
	$res = $person->setProp('DeathAge',Model::P_EVL);

    $res = $person->addAttr('text',Mtype::M_TXT);
	
	$res= $person->addAttr('User',Mtype::M_REF,'/User');	
	
	$res = $person->saveMod();	
	$r = $person->getErrLog ();
	$r->show();

	
	// User 
	
	$obj = new Model('User');
	$res= $obj->deleteMod();
	$res = $obj->addAttr('Code',Mtype::M_STRING);
	$res = $obj->addAttr('Name',Mtype::M_STRING);
	$res = $obj->addAttr('SurName',Mtype::M_STRING);
	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

