<?php
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Model; 

// when running this data will be lost !!

	require_once 'SETUP.php';
	
	// Architecture building block 
			
	$obj = new Model(Config::ABB);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',			M_STRING);
	$res = $obj->addAttr('CodeNm',			M_STRING);
	$res = $obj->addAttr('Alias',			M_STRING);
	$res = $obj->addAttr('ShortDesc',		M_STRING);	
	$res = $obj->addAttr('LongDesc',		M_TXT);
	$res = $obj->addAttr('Owner',			M_REF,	"/".Config::Group);
	$res = $obj->addAttr('In',				M_CREF,	"/".Config::Interfaces."/Of");
	$res = $obj->addAttr('Out',				M_CREF,	"/".Config::Exchange."/OutOf");	
	$res = $obj->setAbstr();
 	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// Application

	$obj = new Model(Config::Application);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme(Config::ABB);	
	$res = $obj->addAttr('Style',			M_REF,	"/".Config::AStyle);
	$res = $obj->addAttr('Authenticity',	M_REF,	"/".Config::SLevel);
	$res = $obj->addAttr('Availability',	M_REF,	"/".Config::SLevel);
	$res = $obj->addAttr('Confidentiality',	M_REF,	"/".Config::SLevel);	
	$res = $obj->addAttr('Integrity',		M_REF,	"/".Config::SLevel);	
	$res = $obj->addAttr('BuiltFrom',		M_CREF,	"/".Config::Component."/Of"); 
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// Component 
	
	$obj = new Model(Config::Component);	
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme(Config::ABB);
	$res = $obj->addAttr(Config::CType,		M_REF,	"/".Config::CType); 	
	$res = $obj->addAttr('Of',				M_REF,	"/".Config::Application); 
	$res = $obj->addAttr('SourceControl',	M_REF,	"/".Config::SControl);
	$res = $obj->addAttr('Url',				M_STRING);
	$res = $obj->addAttr('Queue',			M_STRING);
	$res = $obj->addAttr('OutQueue',		M_STRING);
	$res = $obj->addAttr('BatchNme',		M_STRING);
	$res = $obj->addAttr('Frequency',		M_STRING);
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// $Interface

	$obj = new Model(Config::Interfaces);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',			M_STRING);	
	$res = $obj->addAttr('Of',				M_REF,	"/".Config::ABB);
	$res = $obj->addAttr(Config::IType,		M_REF,	"/".Config::IType);
	$res = $obj->addAttr(Config::IUse,		M_REF,	"/".Config::IUse);	
	$res = $obj->addAttr('Streaming',		M_STRING);
	$res = $obj->addAttr('LongDesc',		M_TXT);
	$res = $obj->addAttr('Content',			M_STRING);	
	$res = $obj->addAttr('UsedBy',			M_CREF,"/".Config::Exchange."/Through");
		
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	

	// Exchange
	
	$obj = new Model(Config::Exchange);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('CodeNm',			M_STRING);
	$res = $obj->addAttr('Through',			M_REF,"/".Config::Interfaces);
	$res = $obj->addAttr('OutOf',			M_REF,"/".Config::ABB);
	$obj->setCkey(['OutOf','Through'],true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";


/*******************************  Codes  ************************/
	
	// Abstract 
	
	$obj = new Model(Config::ACode);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Value',M_STRING);
	$res = $obj->setMdtr('Value',true);
    $res = $obj->setBkey('Value',true);	
	$res = $obj->setAbstr();	

	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Ctype
	
	$obj = new Model(Config::CType);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme(Config::ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// SLevel
	
	$obj = new Model(Config::SLevel);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme(Config::ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// A style

	$obj = new Model(Config::AStyle);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme(Config::ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";


	// Source control

	$obj = new Model(Config::SControl);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme(Config::ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Interface type
		
	$obj = new Model(Config::IType);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme(Config::ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// interface usage
	
	$obj = new Model(Config::IUse);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme(Config::ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	
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