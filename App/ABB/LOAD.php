<?php
		
	require_once 'CLASSDEC.php';	
				
	// Ctype
	
	$obj=new Model($CType);
	$obj->setVal('Value','Message');
	$obj->save();

	$obj=new Model($CType);
	$obj->setVal('Value','Batch');
	$obj->save();

	$obj=new Model($CType);
	$obj->setVal('Value','GUI');
	$obj->save();

	// SLevel
	
	$obj=new Model($SLevel);
	$obj->setVal('Value','Critical');
	$obj->save();

	$obj=new Model($SLevel);
	$obj->setVal('Value','Major');
	$obj->save();

	$obj=new Model($SLevel);
	$obj->setVal('Value','Minor');
	$obj->save();

	$obj=new Model($SLevel);
	$obj->setVal('Value','None');
	$obj->save();

	// A style

	$obj=new Model($AStyle);
	$obj->setVal('Value','Recoverable Transactions');
	$obj->save();

	$obj=new Model($AStyle);
	$obj->setVal('Value','Low Latency, High Capacity');
	$obj->save();

	$obj=new Model($AStyle);
	$obj->setVal('Value','Data Warehousing, Reporting & Analytics');
	$obj->save();

	$obj=new Model($AStyle);
	$obj->setVal('Value','Data Broadcast & Streaming');
	$obj->save();

	$obj=new Model($AStyle);
	$obj->setVal('Value','GUI Applications');
	$obj->save();

	// Source control

	$obj=new Model($SControl);
	$obj->setVal('Value','Internal');
	$obj->save();

	$obj=new Model($SControl);
	$obj->setVal('Value','External');
	$obj->save();	

	// Interface type
	
	$obj=new Model($IType);
	$obj->setVal('Value','Message');
	$obj->save();

	$obj=new Model($IType);
	$obj->setVal('Value','Noticiation');
	$obj->save();	

	$obj=new Model($IType);
	$obj->setVal('Value','Batch');
	$obj->save();

	$obj=new Model($IType);
	$obj->setVal('Value','DataBase');
	$obj->save();

	// interface usage
	
	$obj=new Model($IUse);
	$obj->setVal('Value','Internal');
	$obj->save();

	$obj=new Model($IUse);
	$obj->setVal('Value','External');
	$obj->save();


