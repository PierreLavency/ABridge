<?php
	

// when running this data will be lost !!

	require_once 'CLASSDEC.php';
	
	// CodeVal
		
	$codeval = new Model($CodeVal);
	$res= $codeval->deleteMod();
	
	$res = $codeval->addAttr('Name',M_STRING);

	$res = $codeval->addAttr('ValueOf',M_REF,'/'.$Code);
	$res=$codeval->setProp('ValueOf', Model::P_MDT); 
		
	
	$res = $codeval->saveMod();
	echo "<br>".$CodeVal."<br>";
	$r = $codeval-> getErrLog ();
	$r->show();

	// Code
	
	$code = new Model($Code);
	$res= $code->deleteMod();
		
	$res = $code->addAttr('Name',M_STRING); 
	$res=$code->setProp('Name',Model::P_BKY);// Unique
		

	$res = $code->addAttr('Values',M_CREF,'/'.$CodeVal.'/ValueOf');

	echo $Code."<br>";	
	$res = $code->saveMod();
	$r->show();
	
	// code are created in META
	
	$sex = new Model($Code);
	$res = $sex->setVal('Name','Sexe');
	$sex_id = $sex->save();	
	
	$r = $sex-> getErrLog ();
	$r->show();	
	
	$country = new Model($Code);
	$res = $country->setVal('Name','Country');
	$country_id = $country->save();	
	
	$r = $country-> getErrLog ();
	$r->show();
	
	// Student 
		
	$obj = new Model($Student);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);

	$res = $obj->addAttr('SurName',M_STRING);
	
	$res = $obj->addAttr('BirthDay',M_DATE);
	

	$res = $obj->addAttr('Sexe',M_CODE,'/'.$Code."/$sex_id/Values");	
	

	$res = $obj->addAttr('Country',M_CODE,'/'.$Code."/$country_id/Values");	


	$res = $obj->addAttr('InscritA',M_CREF,'/'.$Inscription.'/De');

	$obj->addAttr('NbrCours',M_INT);
	$obj->setProp('NbrCours',Model::P_EVL);
	$obj->setProp('NbrCours',Model::P_TMP);
	
	$obj->addAttr('NbrCredits',M_INT);
	$obj->setProp('NbrCredits',Model::P_EVL);
	
	$obj->addAttr('Jason',M_TXT);
	$obj->setProp('Jason',Model::P_EVL);
	$obj->setProp('Jason',Model::P_TMP);
	
	$obj->addAttr('Image',M_STRING);
	
	$obj->addAttr($User,M_REF,'/'.$User);
	$res=$obj->setProp($User,Model::P_BKY);

	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	

	// Cours 
	
	$cours = new Model($Cours);
	$res= $cours->deleteMod();

	$res = $cours->addAttr('Name',M_STRING);
	$res = $cours->addAttr('SuivitPar',M_CREF,'/'.$Inscription.'/A');
	$cours->addAttr('Credits',M_INT);
	$cours->addAttr('Par',M_CREF,'/Charge/De');
	
	$cours->addAttr($User,M_REF,'/'.$User);

	echo $Cours."<br>";	
	$res = $cours->saveMod();	
	$r = $cours-> getErrLog ();
	$r->show();
	
	// Inscription

	$inscription = new Model($Inscription);
	$res= $inscription->deleteMod();
	

	$res = $inscription->addAttr('De',M_REF,'/'.$Student);
	$res=$inscription->setProp('De', Model::P_MDT); 
	$res = $inscription->addAttr('A',M_REF,'/'.$Cours);
	$res=$inscription->setProp('A', Model::P_MDT); 
	$inscription->setCkey(['De','A'],true);

	echo $Inscription."<br>";	
	$res = $inscription->saveMod();	
	$r   = $inscription-> getErrLog ();
	$r->show();

	// Prof
	
	$prof=new Model($Prof);
	$res= $prof->deleteMod();
	
	$prof->addAttr('Name',M_STRING);
	$prof->addAttr('SurName',M_STRING);
	$prof->addAttr('BirthDay',M_DATE);
	$prof->addAttr('Sexe',M_CODE,'/Code/1/Values');
	$prof->addAttr('Donne',M_CREF,'/Charge/Par');
	
	
	$prof->addAttr($User,M_REF,'/'.$User);
	$res=$prof->setProp($User,Model::P_BKY);
	
	echo "Prof<br>";	
	$prof->saveMod();	
	$r = $prof-> getErrLog ();
	$r->show();

	// Charge
	
	$Charge=new Model('Charge');
	$res= $Charge->deleteMod();
	
	
	$Charge->addAttr('De',M_REF,'/Cours');
	$Charge->addAttr('Par',M_REF,'/Prof');
	$Charge->setCkey(['De','Par'],true);

	echo "Charge<br>";	
	$Charge->saveMod();	
	$r = $Charge-> getErrLog ();
	$r->show();
	
	
/*******************************  User  ************************/

	$bindings = [$Session=>$Session,$User=>$User,$Role=>$Role,$Distribution=>$Distribution];
	
	UtilsC::createMods($bindings);
	
	// User
		
	$obj = new Model($User);
	
	$res = $obj->addAttr($Group,M_REF,'/'.$Group);	
	$res = $obj->addAttr('Profile',M_CREF,'/'.$Student.'/'.$User);
	$res = $obj->addAttr('ProfProfile',M_CREF,'/'.$Prof.'/'.$User);
	
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
 
 	// Group
	
	$obj = new Model($Group);
	$res= $obj->deleteMod();

 	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('Users',M_CREF,'/'.$User.'/'.$Group);
	
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";		
	
 
 /************** end user ***************************/
 
 
 // Pages
	
	$obj = new Model($Page);
	$res= $obj->deleteMod();

 	$res = $obj->addAttr('Subject',M_STRING);
 	$res = $obj->addAttr('Content',M_HTML);
	
	echo "$Page<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();		

	
	
	