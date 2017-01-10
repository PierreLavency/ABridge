<?php

require_once("Path.php"); 
require_once("Model.php"); 
require_once("View.php"); 

function viewCasesXref()
{
	$db = getBaseHandler('dataBase','test');
	$db->setLogLevl(0);
	initStateHandler('dir', 'dataBase','test');
	
	$db->beginTrans();
	$x=new Model('dir');
	$x->deleteMod();

	$x->addAttr('Name',M_STRING);
	$x->addAttr('Father',M_REF,'/dir');
	$x->addAttr('FatherOf',M_CREF,'/dir/Father');
	$x->addAttr('Mother',M_REF,'/dir');
	$x->addAttr('MotherOf',M_CREF,'/dir/Mother');
	$x->saveMod();

	$x=new Model('dir');
	$x->setVal('Name','Name_1');
	$x->save();

	for ($i=2;$i<10;$i++) {
		$y = new Model('dir');
		$name = 'Name_'.$i;
		$y->setVal('Name',$name);
		$y->setVal('Father',1);
		$y->setVal('Mother',1);
		$y->save();
	}

	
	for ($i=2;$i<3;$i++) {
		for ($j=1;$j<12;$j++) {
			$z= new Model('dir');
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
	$path = '/dir/'.$v;
	
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
