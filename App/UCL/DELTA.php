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
	$Page ='Page';
	$Group = 'UGroup';
	
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


	$res = $obj->addAttr('RoleDefault',M_CODE,'/./Play');
	
	echo "User<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	
	// Role
		

	// Distribution


	// Session 
	
    // Pages
	
	// Group
	
	