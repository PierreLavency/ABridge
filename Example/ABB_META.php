<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

// when running this data will be lost !!

	$db = getBaseHandler('dataBase','abb');
	initStateHandler('ABB', 'dataBase','abb');
	initStateHandler('Application', 'dataBase','abb');
	initStateHandler('Component', 'dataBase','abb');
	initStateHandler('Interface', 'dataBase','abb');
	initStateHandler('Exchange', 'dataBase','abb');
	
	$db->beginTrans();
	
	// Architecture building block 
	
	$obj = new Model('ABB');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('In',M_CREF,'/Interface/Of');
	$res = $obj->addAttr('Out',M_CREF,'/Exchange/OutOf');
	$res = $obj->addAttr('ShortDesc',M_STRING);	
	$res = $obj->addAttr('LongDesc',M_TXT);	
	$res = $obj->setAbstr();
 	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

	
	// Application
	
	$obj = new Model('Application');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Owner',M_STRING);		
	$res = $obj->addAttr('BuiltFrom',M_CREF,'/Component/Of'); 
	
	$res = $obj->setInhNme('ABB');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	
	// Component 
	
	$obj = new Model('Component');	
	$res= $obj->deleteMod();
	
	$res = $obj->addAttr('Type',M_REF,'/CType'); 	
	$res = $obj->addAttr('Of',M_REF,'/Application'); 

	$res = $obj->addAttr('Url',M_STRING);
	$res = $obj->addAttr('Queue',M_STRING);
	$res = $obj->addAttr('BatchNme',M_STRING);
	
	$res = $obj->setInhNme('ABB');

	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	
	
	// Interface

	$obj = new Model('Interface');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('Of',M_REF,'/ABB');
	$res = $obj->addAttr('UsedBy',M_CREF,'/Exchange/Through');
		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	

	// Exchange
	
	$obj = new Model('Exchange');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
	$res = $obj->addAttr('Through',M_REF,'/Interface');
	$res = $obj->addAttr('OutOf',M_REF,'/ABB');
	$obj->setCkey(['OutOf','Through'],true);	
	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	
	
	$db->commit();
	
