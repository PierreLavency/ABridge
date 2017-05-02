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
	$prof=new Model($Prof);
	

	
	echo "Prof<br>";	
	$prof->saveMod();	
	$r = $prof-> getErrLog ();
	$r->show();
	// Charge
	


	// User
	$obj = new Model($User);
	

	
	echo "User<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	
	// Role
		

	
	// Distribution



	
	// Session 
	

	