<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");

// when running this data will be lost !!

	$fb->beginTrans();
	$db->beginTrans();
	
	// CodeVal
		
	$codeval = new Model($CodeVal);
	$res= $codeval->deleteMod();
	
	$res = $codeval->addAttr('Name');

	$path='/'.$Code;
	$res = $codeval->addAttr('ValueOf',M_REF,$path);
	$res=$codeval->setMdtr('ValueOf',true); // Mdtr
		
	$res = $codeval->saveMod();	

	$r = $codeval-> getErrLog ();

	// Code
	
	$code = new Model($Code);
	$res= $code->deleteMod();
		
	$res = $code->addAttr('Name'); 
	$res=$code->setBkey('Name',true);// Unique
		
	$path='/'.$CodeVal.'/ValueOf';
	$res = $code->addAttr('Values',M_CREF,$path);
		
	$res = $code->saveMod();

	// code are created in META
	
	$sex = new Model($Code);
	$res = $sex->setVal('Name','Sexe');
	$sex_id = $sex->save();	
	
	$country = new Model($Code);
	$res = $country->setVal('Name','Country');
	$country_id = $country->save();	
	
	$r = $code-> getErrLog ();
	
	// Sudent 
		
	$student = new Model($Student);
	$res= $student->deleteMod();

	$res = $student->addAttr('Name');

	$res = $student->addAttr('SurName');
	
	$res = $student->addAttr('BirthDay',M_DATE);
	
	$path='/'.$Code."/$sex_id/Values";
	$res = $student->addAttr('Sexe',M_CODE,$path);	
	
	$path='/'.$Code."/$country_id/Values";
	$res = $student->addAttr('Country',M_CODE,$path);	

	$res = $student->saveMod();	

	$r = $student-> getErrLog ();

	
	// Person
	
	$person = new Model($Person);
	$res= $person->deleteMod();

	$res = $person->addAttr('Name');
	$res = $person->addAttr('SurName');
	$res = $person->addAttr('BirthDay',M_DATE);
	
	$path='/'.$Code."/$sex_id/Values";
	$res = $person->addAttr('Sexe',M_CODE,$path);	
	
	$path='/'.$Code."/$country_id/Values";
	$res = $person->addAttr('Country',M_CODE,$path);	

	$ModP=modPath($Person);	
	$res = $person->addAttr('Father',M_REF,$ModP);
	$res = $person->addAttr('Mother',M_REF,$ModP);

	$path='/'.$Person.'/Father';
	$res = $person->addAttr('FatherOf',M_CREF,$path);	

	$path='/'.$Person.'/Mother';
	$res = $person->addAttr('MotherOf',M_CREF,$path);	

	$res = $person->saveMod();	
	$r = $person->getErrLog ();
	
	$fb->commit();	
	$db->commit();
	

?>	