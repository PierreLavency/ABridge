<?php
require_once("Request.php");
require_once("Handle.php");
require_once("View.php"); 

function viewCasesXref()
{
	$db = getBaseHandler('dataBase','test');
	$db->setLogLevl(0);
	initStateHandler('viewCasesXref', 'dataBase','test');
	
	$db->beginTrans();
	$x=new Model('viewCasesXref');
	$x->deleteMod();

	$x->addAttr('Name',M_STRING);
	$x->addAttr('Father',M_REF,'/viewCasesXref');
	$x->addAttr('FatherOf',M_CREF,'/viewCasesXref/Father');
	$x->addAttr('Mother',M_REF,'/viewCasesXref');
	$x->addAttr('MotherOf',M_CREF,'/viewCasesXref/Mother');
	$x->saveMod();

	$x=new Model('viewCasesXref');
	$x->setVal('Name','Name_1');
	$x->save();

	for ($i=2;$i<10;$i++) {
		$y = new Model('viewCasesXref');
		$name = 'Name_'.$i;
		$y->setVal('Name',$name);
		$y->setVal('Father',1);
		$y->setVal('Mother',1);
		$y->save();
	}

	
	for ($i=2;$i<3;$i++) {
		for ($j=1;$j<12;$j++) {
			$z= new Model('viewCasesXref');
			$n = (10*$i)+$j;
			$name = 'Name_'.$n;
			$z->setVal('Name',$name);
			$z->setVal('Father',$i);
			$z->setVal('Mother',$i);
			$z->save();
		}
	}

	
	$db->Commit();

	$v = 2;	
	$path = '/viewCasesXref/'.$v;
	
	$test=[];
	$n=0;
	$test[$n]=[$v,$path,V_S_CREA,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_READ,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_UPDT,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_DELT,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_SLCT,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_REF,$n];
	$n++;
	$test[$n]=[$v,$path,V_S_CREF,$n];
	

	return $test;
}
