<?php
	
// when running this data will be lost !!

use ABridge\ABridge\UtilsC; 
use ABridge\ABridge\Model; 

require_once 'SETUP.php';

	
/*******************************  User  ************************/

	$bindings = [
			Config::Session=>Config::Session,
			Config::User=>Config::User,
			Config::Role=>Config::Role,
			Config::Distribution=>Config::Distribution
			
	];	
	UtilsC::createMods($bindings);	
	
	// User	
	$obj = new Model(Config::User);
	
	$res = $obj->addAttr(Config::Group,M_REF,'/'.Config::Group);	
	
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Group
	
	$obj = new Model(Config::Group);
	$res= $obj->deleteMod();

 	$res = $obj->addAttr('Name',		M_STRING);
	$res = $obj->addAttr('Users',		M_CREF,'/'.Config::User.'/'.Config::Group);
    $res = $obj->setBkey('Name',true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	