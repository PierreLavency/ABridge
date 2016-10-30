<?php

require_once("UnitTest.php");

$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* CODE and DB injection */');

/**************************************/

// init Meta
require_once("Model.php"); 

$db=getBaseHandler ('fileBase',$logName);
	$line = "db=getBaseHandler ('fileBase',$logName);"; $log->logLine($line);

	
// load Codes

$ModN= 'Code';

$s=initStateHandler ($ModN,'fileBase',$logName);
	$line = "s=initStateHandler ($ModN,'fileBase',$logName);"; $log->logLine($line);

$db->inject('Class_code_test');	
	$line = "db->inject('Class_code_test');	"; $log->logLine($line);
	
$code1=new Model($ModN,1);
	$line = "code1=new Model($ModN,1);"; $log->logLine($line);

$nme1=$code1->getVal('Name');	
	$line = "$nme1=code1->getVal('Name');"; $log->logLine($line);


// META 	

$ModN= 'Person';

$s=initStateHandler ($ModN,'fileBase',$logName);
	$line = "s=initStateHandler ($ModN,'fileBase',$logName);"; $log->logLine($line);

$mod = new Model($ModN);

$res = $mod->addAttr('Name');
	$line = "$res = mod->addAttr('Name');"; $log->logLine($line);

$res = $mod->addAttr('SurName');
	$line = "$res = mod->addAttr('SurName');"; $log->logLine($line);

$res = $mod->addAttr('BirthDay',M_DATE);
	$line = "$res = mod->addAttr('BirthDay',M_DATE);"; $log->logLine($line);	

$res = $mod->addAttr('Father',M_REF,$ModN);
	$line = "$res = mod->addAttr('Father',M_REF,$ModN);"; $log->logLine($line);	

$res = $mod->addAttr('Mother',M_REF,$ModN);
	$line = "$res = mod->addAttr('Father',M_REF,$ModN);"; $log->logLine($line);	
	
$path='/'.$ModN.'/Father';
$res = $mod->addAttr('FatherOf',M_CREF,$path);	
	$line = "$res = mod->addAttr('Children',M_CREF,$path);"; $log->logLine($line);

$path='/'.$ModN.'/Mother';
$res = $mod->addAttr('MotherOf',M_CREF,$path);	
	$line = "$res = mod->addAttr('Children',M_CREF,$path);"; $log->logLine($line);

$path='/Code/1/Values';
$res = $mod->addAttr('Sexe',M_CODE,$path);	
	$line = "$res = mod->addAttr('Sexe',M_CODE,$path);"; $log->logLine($line);

$res = $mod->saveMod();	
	$line = "$res = mod->saveMod();"; $log->logLine($line);	

$log->includeLog($mod-> getErrLog ());	

// Do 

$p7 = new Model($ModN);

$res=$p7->setVal('Name','Arnould');
	$line = "$res=p7->setVal('Name','Arnould');"; $log->logLine($line);

$res=$p7->setVal('SurName','Dominique');
	$line = "$res=p7->setVal('SurName','Dominique');"; $log->logLine($line);

$res=$p7->setVal('BirthDay','18-05-1960');
	$line = "$res=p7->setVal('BirthDay','18-05-1960');"; $log->logLine($line);

$res=$p7->setVal('Sexe',3);
	$line = "$res=p7->setVal('Sexe',3);"; $log->logLine($line);
	
$id7 = $p7->save();	
	$line = "$id7 = p7->save();"; $log->logLine($line);	

$log->includeLog($p7-> getErrLog ());	

// Moi

$p1 = new Model($ModN);

$res = $p1->getValues('Sexe');
	$line = "res = p1->getValues('Sexe');"; $log->logLine($line);

$r=implode(',',$res);
	$line = "$r=implode(',',res);"; $log->logLine($line);
	
$res=$p1->setVal('Name','Lavency');
	$line = "$res=p1->setVal('Name','Lavency');"; $log->logLine($line);

$res=$p1->setVal('SurName','Pierre');
	$line = "$res=p1->setVal('SurName','Pierre');"; $log->logLine($line);

$res=$p1->setVal('BirthDay','26-05-1959');
	$line = "$res=p1->setVal('BirthDay','26-05-1959');"; $log->logLine($line);

$res=$p1->setVal('Sexe',2);
	$line = "$res=p1->setVal('Sexe',2);"; $log->logLine($line);
	
$id1 = $p1->save();	
	$line = "$id1 = p1->save();"; $log->logLine($line);	

$log->includeLog($p1-> getErrLog ());	

// Ren

$p2 = new Model($ModN);

$res=$p2->setVal('Name','Lavency');
	$line = "$res=p2->setVal('Name','Lavency');"; $log->logLine($line);

$res=$p2->setVal('SurName','Renaud');
	$line = "$res=p2->setVal('SurName','Renaud');"; $log->logLine($line);

$res=$p2->setVal('BirthDay','24-04-1988');
	$line = "$res=p2->setVal('BirthDay','24-04-1988');"; $log->logLine($line);

$res=$p2->setVal('Sexe',2);
	$line = "$res=p2->setVal('Sexe',2);"; $log->logLine($line);

$res=$p2->setVal('Father',$id1);
	$line = "$res=p2->setVal('Father',$id1);"; $log->logLine($line);

$res=$p2->setVal('Mother',$id7);
	$line = "$res=p2->setVal('Mother',$id7);"; $log->logLine($line);
	
$id2 = $p2->save();	
	$line = "$id2 = p2->save();"; $log->logLine($line);	

$log->includeLog($p2-> getErrLog ());

// Papa

$p3 = new Model($ModN);

$res=$p3->setVal('Name','Lavency');
	$line = "$res=p3->setVal('Name','Lavency');"; $log->logLine($line);

$res=$p3->setVal('SurName','Marius');
	$line = "$res=p3->setVal('SurName','Marius');"; $log->logLine($line);

$res=$p3->setVal('BirthDay','19-09-1926');
	$line = "$res=p3->setVal('BirthDay','19-09-1926');"; $log->logLine($line);

$res=$p3->setVal('Sexe',2);
	$line = "$res=p3->setVal('Sexe',2);"; $log->logLine($line);

$id3 = $p3->save();	
	$line = "$id3 = p3->save();"; $log->logLine($line);	

$log->includeLog($p3-> getErrLog ());

$res=$p1->setVal('Father',$id3);
	$line = "$res=p1->setVal('Father',$id3);"; $log->logLine($line);

$id1=$p1->save();
$line = "$id1=p1->save();"; $log->logLine($line);

// Juliette  

$p4 = new Model($ModN);

$res=$p4->setVal('Name','Lavency');
	$line = "$res=p4->setVal('Name','Lavency');"; $log->logLine($line);

$res=$p4->setVal('SurName','Juliette');
	$line = "$res=p4->setVal('SurName','Juliette');"; $log->logLine($line);

$res=$p4->setVal('BirthDay','22-11-1990');
	$line = "$res=p4->setVal('BirthDay','24-04-1988');"; $log->logLine($line);

$res=$p4->setVal('Sexe',3);
	$line = "$res=p4->setVal('Sexe',3);"; $log->logLine($line);

$res=$p4->setVal('Father',$id1);
	$line = "$res=p4->setVal('Father',$id1);"; $log->logLine($line);

$res=$p4->setVal('Mother',$id7);
	$line = "$res=p4->setVal('Mother',$id7);"; $log->logLine($line);	
	
$id4 = $p4->save();	
	$line = "$id4 = p4->save();"; $log->logLine($line);	

$log->includeLog($p4-> getErrLog ());

// Estelle

$p5= new Model($ModN);

$res=$p5->setVal('Name','Lavency');
	$line = "$res=p5->setVal('Name','Lavency');"; $log->logLine($line);

$res=$p5->setVal('SurName','Estelle');
	$line = "$res=p5->setVal('SurName','Estelle');"; $log->logLine($line);

$res=$p5->setVal('BirthDay','20-07-1995');
	$line = "$res=p5->setVal('BirthDay','20-07-1995');"; $log->logLine($line);

$res=$p5->setVal('Sexe',3);
	$line = "$res=p5->setVal('Sexe',3);"; $log->logLine($line);

$res=$p5->setVal('Father',$id1);
	$line = "$res=p5->setVal('Father',$id1);"; $log->logLine($line);

$res=$p5->setVal('Mother',$id7);
	$line = "$res=p5->setVal('Mother',$id7);"; $log->logLine($line);	
	
$id5 = $p5->save();	
	$line = "$id5 = p5->save();"; $log->logLine($line);	

$log->includeLog($p5-> getErrLog ());

// Maman

$p6 = new Model($ModN);

$res=$p6->setVal('Name','Quoilin');
	$line = "$res=p6->setVal('Name','Quoilin'');"; $log->logLine($line);

$res=$p6->setVal('SurName','Madelein');
	$line = "$res=p6->setVal('SurName','Madelein');"; $log->logLine($line);

$res=$p6->setVal('BirthDay','24-11-1926');
	$line = "$res=p6->setVal('BirthDay',''24-11-1926');"; $log->logLine($line);

$res=$p6->setVal('Sexe',3);
	$line = "$res=p6->setVal('Sexe',3);"; $log->logLine($line);
	
$id6 = $p6->save();	
	$line = "$id6 = p6->save();"; $log->logLine($line);	

$log->includeLog($p6-> getErrLog ());

$res=$p1->setVal('Mother',$id6);
	$line = "$res=p1->setVal('Mother',$id6);"; $log->logLine($line);

$id1=$p1->save();
$line = "$id1=p1->save();"; $log->logLine($line);

// commit 

$res = $db->commit();
	$line = "$res = db->commit;"; $log->logLine($line);

// some errors

$res=$p6->setVal('Sexe',1000);
	$line = "$res=p6->setVal('Sexe',1000);"; $log->logLine($line);

$res=$p6->setVal('Mother',1000);
	$line = "$res=p6->setVal('Mother',1000);"; $log->logLine($line);	

$log->includeLog($p6-> getErrLog ());


/**************************************/
	
$log->saveTest();

// $log->showTest();

?>