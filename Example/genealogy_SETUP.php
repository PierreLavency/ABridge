<?php
	
require_once("Handler.php"); 

	$CodeVal 	='CodeValue';
	$Code 		='Code';
	$Student 	='Student';
	$Cours	 	='Cours';
	$Inscription='Inscription';	
	$Person 	='Person';
//	$default 	=$Code; 
//	$defaultId =1;
	
	
	
	$DBName = 'genealogy'; // should be changed to genealogy / genealogy_test when ok/ko !!
	
	$fb=getBaseHandler('fileBase',$DBName);
	$db=getBaseHandler('dataBase',$DBName);
	
	$s=initStateHandler($CodeVal,		'fileBase',$DBName); // if changed DB tables will ,not be dropped !!
	$s=initStateHandler($Code,			'fileBase',$DBName);
	$s=initStateHandler($Student,		'fileBase',$DBName);
	$s=initStateHandler($Cours	,		'fileBase',$DBName);
	$s=initStateHandler($Inscription,	'fileBase',$DBName);
	$s=initStateHandler($Person,		'dataBase',$DBName);
	
?>	