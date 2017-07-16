<?php
		
require_once 'SETUP.php';	
use ABridge\ABridge\Model; 

	// Roles 	
	
	$RSpec = 
'[
["true","true","true"]
]';

	$obj=new Model(Config::Role);
	$obj->setVal('Name','Root');
	$obj->setVal('JSpec',$RSpec);
	$RootRole=$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";

	$RSpec = 
'[
[["Read"],          "true",         "true"],
[["Read","Update","Delete"],  "|Session",    {"Session":"id"}],
[["Read","Update"],  "|User",       {"User":"id<>User"}]
]';

	$obj=new Model(Config::Role);
	$obj->setVal('Name','Default');
	$obj->setVal('JSpec',$RSpec);
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	
	// User
	
	$obj=new Model(Config::User);
	$obj->setVal('UserId','Root');
	$RootUser=$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";

	// Distribution
	
	$obj=new Model(Config::Distribution);
	$obj->setVal('ofRole',$RootRole);
	$obj->setVal('toUser',$RootUser);		
	$res=$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	