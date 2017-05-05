<?php
	


	$Code = 'Code';	
	$CodeVal= 'CodeValue';
	$Student = 'Student';
	$Inscription = 'Inscription';
	$Cours = 'Cours';
	$User ='User';
	$Role = 'Role';
	$Session ='Session';
	$Distribution = 'Distribution';
	$Prof = 'Prof';
	
	// CodeVal
		


	// Code
	

	// code are created in META
	

	
	// Student 
		


	// Cours 
	

	// Inscription



	// Prof

	// Charge
	


	// User

	
	
	// Role
		

	
	// Distribution


	// Session 
	
	$obj = new Model($Session);
	$res= $obj->deleteMod();

	$res = $obj->addAttr($User,M_REF,'/'.$User);
	$res = $obj->addAttr($Role,M_REF,'/'.$Role);
	$res = $obj->addAttr('Comment',M_STRING);
	$res = $obj->addAttr('BKey',M_STRING);
	$res = $obj->setBkey('BKey',true);
		
	$res = $obj->saveMod();
	$r = $obj->getErrLog ();
	$r->show();
	