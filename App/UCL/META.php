<?php
	

// when running this data will be lost !!


	$Code = 'Code';	
	$CodeVal= 'CodeValue';
	$Student = 'Student';
	$Inscription = 'Inscription';
	$Cours = 'Cours';
	$User ='User';
	$Role = 'Role';
	$Session ='Session';
	$Distribution = 'Distribution';
	
	
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
		
	$obj = new Model($Student);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);

	$res = $obj->addAttr('SurName',M_STRING);
	
	$res = $obj->addAttr('BirthDay',M_DATE);
	
	$path='/'.$Code."/$sex_id/Values";
	$res = $obj->addAttr('Sexe',M_CODE,$path);	
	
	$path='/'.$Code."/$country_id/Values";
	$res = $obj->addAttr('Country',M_CODE,$path);	

	$path='/'.$Inscription.'/De';
	$res = $obj->addAttr('InscritA',M_CREF,$path);

	$obj->addAttr('NbrCours',M_INT,M_P_EVAL);
	$obj->addAttr('NbrCredits',M_INT,M_P_EVALP);
	$obj->addAttr('Jason',M_TXT,M_P_EVAL);
	$obj->addAttr('Image',M_STRING);	
	$obj->addAttr($User,M_REF,'/'.$User);
	
	$res=$obj->setBkey($User,true);

	echo $Student."<br>";	
	$res = $obj->saveMod();	
	$r = $obj-> getErrLog ();
	$r->show();

	// Cours 
	
	$cours = new Model($Cours);
	$res= $cours->deleteMod();

	$res = $cours->addAttr('Name',M_STRING);
	
	$path='/'.$Inscription.'/A';
	$res = $cours->addAttr('SuivitPar',M_CREF,$path);

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
	
	$prof=new Model($Prof);
	$res= $prof->deleteMod();
	
	$prof->addAttr('Name',M_STRING);
	$prof->addAttr('SurName',M_STRING);
	$prof->addAttr('BirthDay',M_DATE);


	$path='/Code/1/Values';
	$prof->addAttr('Sexe',M_CODE,$path);

	$prof->addAttr('Donne',M_CREF,'/Charge/Par');
	$prof->addAttr($User,M_REF,'/'.$User);
	$res=$prof->setBkey($User,true);
	
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
		
	$obj = new Model($User);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('SurName',M_STRING);
	$res = $obj->addAttr('Play',M_CREF,'/'.$Distribution.'/toUser');
	
	$res = $obj->addAttr('Profile',M_CREF,'/'.$Student.'/'.$User);
	$res = $obj->addAttr('ProfProfile',M_CREF,'/'.$Prof.'/'.$User);
	
	echo "User<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

	// Role
		
	$obj = new Model($Role);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('Spec',M_TXT);
	$res = $obj->addAttr('PlayedBy',M_CREF,'/'.$Distribution.'/ofRole');
	
	echo "$Role<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	
	
	// Session
	
	$obj = new Model($Session);
	$res= $obj->deleteMod();

	$res = $obj->addAttr($User,M_REF,'/'.$User);
	$res = $obj->addAttr($Role,M_REF,'/'.$Role);
	$res = $obj->addAttr('Comment',M_STRING);
	$res = $obj->addAttr('BKey',M_STRING);
	$res = $obj->setBkey('BKey',true);
		
	
	echo "Session<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	// Distribution

	$obj = new Model($Distribution);
	$res= $obj->deleteMod();

	$path='/'.$Role;
	$res = $obj->addAttr('ofRole',M_REF,$path);
	$res = $obj->setMdtr('ofRole',true); // Mdtr

	$path='/'.$User;
	$res = $obj->addAttr('toUser',M_REF,$path);
	$res = $obj->setMdtr('toUser',true); // Mdtr

	$obj->setCkey(['ofRole','toUser'],true);
	
	echo "$Distribution<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	
	