<?php

require_once("Path.php"); 
require_once("Model.php"); 
require_once("View.php"); 

function viewCases()
{
	$x=new Model('test');
	$x->deleteMod();

	$x=new Model('test');
	
	$x->addAttr('A1',M_INT);

	$x->addAttr('A2',M_STRING);

	$x->addAttr('A3',M_DATE);

	$x->addAttr('A4',M_TXT);

	$x->setVal('A1',5);
	if ($x->isErr()) {
		$x->getErrLog()->show();
	}

	$x->setVal('A2','aa');
	if ($x->isErr()) {
		$x->getErrLog()->show();
	}

	$x->setVal('A3','2016-12-08');
	if ($x->isErr()) {
		$x->getErrLog()->show();
	}

	$x->setVal('A4','ceci est un texte');
	if ($x->isErr()) {
		$x->getErrLog()->show();
	}
	
	$v = new View($x);
	
	$path = new Path('/test/1');

	$v->setAttrList(['A1','A2'],V_S_REF);
	
	$test = [];
	$n=0;
	$test[$n]=[$v,$path,V_S_CREA,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_READ,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_UPDT,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_DELT,$n];	
	$n++;
	$test[$n]=[$v,$path,V_S_REF,$n];
	$n++;
	$test[$n]=[$v,$path,V_S_CREF,$n];
	
	return $test;
}
