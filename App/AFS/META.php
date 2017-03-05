<?php

	require_once 'Model.php';

	function createData($id,$B,$D) 
	{	
		for ($i=1;$i<$B;$i++) {
			$x = new Model('Dir');
			$name = 'D_'.$id.'.'.$i;
			$x->setVal('Name',$name);
			$x->setVal('Father',$id);
			$id2= $x->save();
			$x = new Model('Fle');
			$name = 'F_'.$id.'.'.$i;
			$x->setVal('Name',$name);
			$x->setVal('Father',$id);
			$x->save();
			if($D > 0) {
				createData($id2,$B,$D-1);
			}			
		}
	}
	
	$x = new Model('Afs');	
	$x->deleteMod();
	$x->setAbstr(); 
	$x->addAttr('Name',M_STRING);	
	$x->saveMod();
    $r = $x-> getErrLog ();
	$r->show();
	
	$x=new Model('Dir');
	$x->deleteMod();
	$x->setInhNme('Afs');
	$x->addAttr('Father',M_REF,'/Dir');	
	$x->addAttr('FatherOfD',M_CREF,'/Dir/Father');
	$x->addAttr('FatherOfF',M_CREF,'/Fle/Father');
	$x->saveMod();
    $r = $x-> getErrLog ();
	$r->show();
		
	$x=new Model('Fle');
	$x->deleteMod();
	$x->setInhNme('Afs');
	$x->addAttr('Father',M_REF,'/Dir');
	$x->saveMod();
    $r = $x-> getErrLog ();
	$r->show();	

	$x=new Model('Dir');
	$x->setVal('Name','/');
	$id = $x->save();
	
	createData($id,3,2);


