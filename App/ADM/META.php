<?php
	
	require_once 'CLASSDEC.php';
	require_once 'UtilsC.php'; 
	
/*******************************  User  ************************/
	$Adm ='Admin';
	$bindings = [$Adm=>$Adm];	
	UtilsC::createMods($bindings);	
	