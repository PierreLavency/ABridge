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
	
	// CodeVal
		


	// Code
	

	// code are created in META
	

	
	// Student 
		


	// Cours 
	

	// Inscription



	// Prof
	

	// Charge
	


	// User
	$obj = new Model($User);
	
	$res = $obj->addAttr('Profile',M_CREF,'/'.$Student.'/'.$User);
	
	echo "User<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	
	// Role
		

	
	// Distribution



	
	// Session 
	

	