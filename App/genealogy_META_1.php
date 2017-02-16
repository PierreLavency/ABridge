<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");

// when running this data will be lost !!

	$ctrl = new Controler($config);
	$ctrl->beginTrans();

	$Code = 'Code';	
	$CodeVal= 'CodeValue';
	$Student = 'Student';
	$Cours= 'Cours';
	$Inscription='Inscription';

	
	// Cours 

	
	$cours = new Model($Cours);
	$res= $cours->deleteMod();

	$res = $cours->addAttr('Name');

	$res = $cours->saveMod();	
	$r = $cours-> getErrLog ();

	// Inscription

	$inscription = new Model($Inscription);
	$res= $inscription->deleteMod();
	
	$path='/'.$Student;
	$res = $inscription->addAttr('De',M_REF,$path);
	$res = $inscription->setMdtr('De',true); // Mdtr

	$path='/'.$Cours;
	$res = $inscription->addAttr('A',M_REF,$path);
	$res = $inscription->setMdtr('A',true); // Mdtr

	$res = $inscription->saveMod();	
	$r   = $inscription-> getErrLog ();
	
	// Cref
	
	$student = new Model($Student);
	$path='/'.$Inscription.'/De';
	$res = $student->addAttr('InscritA',M_CREF,$path);
	$res = $student->saveMod();
	
	$path='/'.$Inscription.'/A';
	$res = $cours->addAttr('SuivitPar',M_CREF,$path);
	$res = $cours->saveMod();
	
	$ctrl->commit();	

	