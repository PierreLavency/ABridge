<?php
	

// when running this data will be lost !!


	$Code = 'Code';	
	$CodeVal= 'CodeValue';
	$Student = 'Student';
	$Inscription = 'Inscription';
	$Cours = 'Cours';
	
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
		
	$obj = new Model('Role');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('Spec',M_TXT);

	echo "Role<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	
	// Session 
	
	$obj = new Model('Session');

	$res = $obj->addAttr('Role',M_REF,'/Role');

	echo "Session<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	