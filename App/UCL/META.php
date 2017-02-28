<?php
	

// when running this data will be lost !!


	$Code = 'Code';	
	$CodeVal= 'CodeValue';
	$Student = 'Student';
	$Inscription = 'Inscription';
	$Cours = 'Cours';
	
	// CodeVal
		
	$codeval = new Model($CodeVal);
	$res= $codeval->deleteMod();
	
	$res = $codeval->addAttr('Name',M_STRING);

	$path='/'.$Code;
	$res = $codeval->addAttr('ValueOf',M_REF,$path);
	$res=$codeval->setMdtr('ValueOf',true); // Mdtr
		
	
	$res = $codeval->saveMod();
	echo "<br>".$CodeVal."<br>";
	$r = $codeval-> getErrLog ();
	$r->show();

	// Code
	
	$code = new Model($Code);
	$res= $code->deleteMod();
		
	$res = $code->addAttr('Name',M_STRING); 
	$res=$code->setBkey('Name',true);// Unique
		
	$path='/'.$CodeVal.'/ValueOf';
	$res = $code->addAttr('Values',M_CREF,$path);

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
		
	$student = new Model($Student);
	$res= $student->deleteMod();

	$res = $student->addAttr('Name',M_STRING);

	$res = $student->addAttr('SurName',M_STRING);
	
	$res = $student->addAttr('BirthDay',M_DATE);
	
	$path='/'.$Code."/$sex_id/Values";
	$res = $student->addAttr('Sexe',M_CODE,$path);	
	
	$path='/'.$Code."/$country_id/Values";
	$res = $student->addAttr('Country',M_CODE,$path);	

	$path='/'.$Inscription.'/De';
	$res = $student->addAttr('InscritA',M_CREF,$path);

	$student->addAttr('NbrCours',M_INT,M_P_EVAL);
	$student->addAttr('NbrCredits',M_INT,M_P_EVALP);
	$student->addAttr('Jason',M_TXT,M_P_EVAL);
	$student->addAttr('Image',M_STRING);	
	$student->addAttr('User',M_REF,'/User');

	echo $Student."<br>";	
	$res = $student->saveMod();	
	$r = $student-> getErrLog ();
	$r->show();

	// Cours 
	
	$cours = new Model($Cours);
	$res= $cours->deleteMod();

	$res = $cours->addAttr('Name',M_STRING);
	
	$path='/'.$Inscription.'/A';
	$res = $cours->addAttr('SuivitPar',M_CREF,$path);

	$cours->addAttr('Credits',M_INT);
	
	$cours->addAttr('Par',M_CREF,'/Charge/De');
	$cours->addAttr('User',M_REF,'/User');

	echo $Cours."<br>";	
	$res = $cours->saveMod();	
	$r = $cours-> getErrLog ();
	$r->show();
	
	// Inscription

	$inscription = new Model($Inscription);
	$res= $inscription->deleteMod();
	
	$path='/'.$Student;
	$res = $inscription->addAttr('De',M_REF,$path);
	$res = $inscription->setMdtr('De',true); // Mdtr

	$path='/'.$Cours;
	$res = $inscription->addAttr('A',M_REF,$path);
	$res = $inscription->setMdtr('A',true); // Mdtr

	$inscription->setCkey(['De','A'],true);

	echo $Inscription."<br>";	
	$res = $inscription->saveMod();	
	$r   = $inscription-> getErrLog ();
	$r->show();

	// Prof
	
	$prof=new Model('Prof');
	$res= $prof->deleteMod();
	
	$prof->addAttr('Name',M_STRING);
	$prof->addAttr('SurName',M_STRING);
	$prof->addAttr('BirthDay',M_DATE);


	$path='/Code/1/Values';
	$prof->addAttr('Sexe',M_CODE,$path);

	$prof->addAttr('Donne',M_CREF,'/Charge/Par');
	$prof->addAttr('User',M_REF,'/User');

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

	// User
		
	$obj = new Model('User');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('SurName',M_STRING);

	echo "User<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

	// Session
	
	$obj = new Model('Session');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('User',M_REF,'/User');
 	$res = $obj->addAttr('Comment',M_STRING);

	echo "Seesion<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	