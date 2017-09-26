<?php

use ABridge\ABridge\Mod\Model; 
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Apps\UsrApp;
use ABridge\ABridge\Apps\AdmApp;

// when running this data will be lost !!

	require_once 'SETUP.php';

	AdmApp::loadMeta();
	UsrApp::loadMeta();
	
	// Architecture building block 
			
	$obj = new Model(Config::ABB);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',			Mtype::M_STRING);
	$res = $obj->addAttr('CodeNm',			Mtype::M_STRING);
	$res = $obj->addAttr('Alias',			Mtype::M_STRING);
	$res = $obj->addAttr('ShortDesc',		Mtype::M_STRING);	
	$res = $obj->addAttr('LongDesc',		Mtype::M_TXT);
	$res = $obj->addAttr('Owner',			Mtype::M_REF,	"/".Config::Group);
	$res = $obj->addAttr('In',				Mtype::M_CREF,	"/".Config::Interfaces."/Of");
	$res = $obj->addAttr('Out',				Mtype::M_CREF,	"/".Config::Exchange."/OutOf");	
	$res = $obj->setAbstr();
 	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// Application

	$obj = new Model(Config::Application);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme(Config::ABB);	
	$res = $obj->addAttr('Style',			Mtype::M_REF,	"/".Config::AStyle);
	$res = $obj->addAttr('Authenticity',	Mtype::M_REF,	"/".Config::SLevel);
	$res = $obj->addAttr('Availability',	Mtype::M_REF,	"/".Config::SLevel);
	$res = $obj->addAttr('Confidentiality',	Mtype::M_REF,	"/".Config::SLevel);	
	$res = $obj->addAttr('Integrity',		Mtype::M_REF,	"/".Config::SLevel);	
	$res = $obj->addAttr('BuiltFrom',		Mtype::M_CREF,	"/".Config::Component."/Of"); 
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// Component 
	
	$obj = new Model(Config::Component);	
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme(Config::ABB);
	$res = $obj->addAttr(Config::CType,		Mtype::M_REF,	"/".Config::CType); 	
	$res = $obj->addAttr('Of',				Mtype::M_REF,	"/".Config::Application); 
	$res = $obj->addAttr('SourceControl',	Mtype::M_REF,	"/".Config::SControl);
	$res = $obj->addAttr('Url',				Mtype::M_STRING);
	$res = $obj->addAttr('Queue',			Mtype::M_STRING);
	$res = $obj->addAttr('OutQueue',		Mtype::M_STRING);
	$res = $obj->addAttr('BatchNme',		Mtype::M_STRING);
	$res = $obj->addAttr('Frequency',		Mtype::M_STRING);
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// $Interface

	$obj = new Model(Config::Interfaces);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',			Mtype::M_STRING);	
	$res = $obj->addAttr('Of',				Mtype::M_REF,	"/".Config::ABB);
	$res = $obj->addAttr(Config::IType,		Mtype::M_REF,	"/".Config::IType);
	$res = $obj->addAttr(Config::IUse,		Mtype::M_REF,	"/".Config::IUse);	
	$res = $obj->addAttr('Streaming',		Mtype::M_STRING);
	$res = $obj->addAttr('LongDesc',		Mtype::M_TXT);
	$res = $obj->addAttr('Content',			Mtype::M_STRING);	
	$res = $obj->addAttr('UsedBy',			Mtype::M_CREF,"/".Config::Exchange."/Through");
		
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	

	// Exchange
	
	$obj = new Model(Config::Exchange);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('CodeNm',			Mtype::M_STRING);
	$res = $obj->addAttr('Through',			Mtype::M_REF,"/".Config::Interfaces);
	$res = $obj->addAttr('OutOf',			Mtype::M_REF,"/".Config::ABB);
	$obj->setCkey(['OutOf','Through'],true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";


/*******************************  Codes  ************************/
	
	// Abstract 
	
	$obj = new Model(Config::ACode);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Value',Mtype::M_STRING);
	$res=$obj->setProp('Value', Model::P_MDT); 
    $res = $obj->setProp('Value',Model::P_BKY);	
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
	