<?php
	
require_once('controler.php');
require_once("ABB_SETUP.php");

// when running this data will be lost !!

	$ctrl = new Controler($config);
	$ctrl->beginTrans();
		
	// Abstract 
	
	$obj = new Model('ACode');
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Value',M_STRING);
	$res = $obj->setMdtr('Value',true);
    $res = $obj->setBkey('Value',true);	
	$res = $obj->setAbstr();
 	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();

	
	// Ctype
	
	$obj = new Model('CType');
	$res= $obj->deleteMod();

	$res = $obj->setInhNme('ACode');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();

	$obj=new Model('CType');
	$obj->setVal('Value','Message');
	$obj->save();
	$obj=new Model('CType');
	$obj->setVal('Value','Batch');
	$obj->save();
	$obj=new Model('CType');
	$obj->setVal('Value','GUI');
	$obj->save();

	// SLevel
	
	$obj = new Model('SLevel');
	$res= $obj->deleteMod();

	$res = $obj->setInhNme('ACode');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();

	$obj=new Model('SLevel');
	$obj->setVal('Value','Critical');
	$obj->save();
	$obj=new Model('SLevel');
	$obj->setVal('Value','Major');
	$obj->save();
	$obj=new Model('SLevel');
	$obj->setVal('Value','Minor');
	$obj->save();
	$obj=new Model('SLevel');
	$obj->setVal('Value','None');
	$obj->save();

	// A style

	$obj = new Model('AStyle');
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme('ACode');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();

	$obj=new Model('AStyle');
	$obj->setVal('Value','Recoverable Transactions');
	$obj->save();

	$obj=new Model('AStyle');
	$obj->setVal('Value','Low Latency, High Capacity');
	$obj->save();

	$obj=new Model('AStyle');
	$obj->setVal('Value','Data Warehousing, Reporting & Analytics');
	$obj->save();

	$obj=new Model('AStyle');
	$obj->setVal('Value','Data Broadcast & Streaming');
	$obj->save();

	$obj=new Model('AStyle');
	$obj->setVal('Value','GUI Applications');
	$obj->save();

	// Source control

	$obj = new Model('SControl');
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme('ACode');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();

	$obj=new Model('SControl');
	$obj->setVal('Value','Internal');
	$obj->save();

	$obj=new Model('SControl');
	$obj->setVal('Value','External');
	$obj->save();
	

	// Interface type
	
	$obj = new Model('IType');
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme('ACode');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();

	$obj=new Model('IType');
	$obj->setVal('Value','Message');
	$obj->save();

	$obj=new Model('IType');
	$obj->setVal('Value','Noticiation');
	$obj->save();

	
	$obj=new Model('IType');
	$obj->setVal('Value','Batch');
	$obj->save();

	$obj=new Model('IType');
	$obj->setVal('Value','DataBase');
	$obj->save();

	// interface usage
	
	$obj = new Model('IUse');
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme('ACode');	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();

	$obj=new Model('IUse');
	$obj->setVal('Value','Internal');
	$obj->save();

	$obj=new Model('IUse');
	$obj->setVal('Value','External');
	$obj->save();

	
	$ctrl->commit();
	
