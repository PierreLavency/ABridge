<?php
	
// when running this data will be lost !!

use ABridge\ABridge\Mod\Model; 
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Apps\Usr;

require_once 'SETUP.php';

	
/*******************************  User  ************************/

	Usr::loadMeta();
	
	// User	
	$obj = new Model(Config::User);
	
	$res = $obj->addAttr(Config::Group,Mtype::M_REF,'/'.Config::Group);	
	
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Group
	
	$obj = new Model(Config::Group);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',		Mtype::M_STRING);
	$res = $obj->addAttr('Users',		Mtype::M_CREF,'/'.Config::User.'/'.Config::Group);
    $res = $obj->setBkey('Name',true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	