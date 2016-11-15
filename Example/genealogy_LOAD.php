<?php
require_once("Model.php"); 
require_once("Handler.php"); 
require_once("genealogy_SETUP.php");

$bb = getStateHandler ($CodeVal);
$xb=$bb->getBase() ;

$xb->beginTrans();
$sextype1 = new Model($CodeVal);
$res = $sextype1->setVal('Name','Male');
$res = $sextype1->setVal('ValueOf',1);
$s1 = $sextype1->save();

$sextype1 = new Model($CodeVal);
$res = $sextype1->setVal('Name','Female');
$res = $sextype1->setVal('ValueOf',1);
$s2 = $sextype1->save();

$res = $xb->commit();
	
for($i=1;$i<3;$i++)
{
	if ($i==1) {
		$ModN=$Person;
		$xb=$db;
	}
	if ($i==2) {
		$xb=$fb;
		$ModN=$Student;
	}
	$xb->beginTrans();
	// Do 
	$p7 = new Model($ModN);
	$res=$p7->setVal('Name','Arnould');
	$res=$p7->setVal('SurName','Dominique');
	$res=$p7->setVal('BirthDay','1960-05-18');
	$res=$p7->setVal('Sexe',$s2);
	$id7 = $p7->save();	
	// Papa
	$p3 = new Model($ModN);
	$res=$p3->setVal('Name','Lavency');
	$res=$p3->setVal('SurName','Marius');
	$res=$p3->setVal('BirthDay','1926-09-19');
	$res=$p3->setVal('Sexe',$s1);
	$id3 = $p3->save();	
	// Maman
	$p6 = new Model($ModN);
	$res=$p6->setVal('Name','Quoilin');
	$res=$p6->setVal('SurName','Madeleine');
	$res=$p6->setVal('BirthDay','1926-11-24');
	$res=$p6->setVal('Sexe',$s2);
	$id6 = $p6->save();	
	// Moi
	$p1 = new Model($ModN);
	$res=$p1->setVal('Name','Lavency');
	$res=$p1->setVal('SurName','Pierre');
	$res=$p1->setVal('BirthDay','1959-05-26');
	$res=$p1->setVal('Sexe',$s1);
	$res=$p1->setVal('Father',$id3);
	$res=$p1->setVal('Mother',$id6);
	$id1 = $p1->save();	
	// Ren
	$p2 = new Model($ModN);
	$res=$p2->setVal('Name','Lavency');
	$res=$p2->setVal('SurName','Renaud');
	$res=$p2->setVal('BirthDay','1988-04-24');
	$res=$p2->setVal('Sexe',$s1);
	$res=$p2->setVal('Father',$id1);
	$res=$p2->setVal('Mother',$id7);
	$id2 = $p2->save();	
	// Juliette  
	$p4 = new Model($ModN);
	$res=$p4->setVal('Name','Lavency');
	$res=$p4->setVal('SurName','Juliette');
	$res=$p4->setVal('BirthDay','1988-04-24');
	$res=$p4->setVal('Sexe',$s2);
	$res=$p4->setVal('Father',$id1);
	$res=$p4->setVal('Mother',$id7);
	$id4 = $p4->save();	
	// Estelle
	$p5= new Model($ModN);
	$res=$p5->setVal('Name','Lavency');
	$res=$p5->setVal('SurName','Estelle');
	$res=$p5->setVal('BirthDay','1995-07-20');
	$res=$p5->setVal('Sexe',$s2);
	$res=$p5->setVal('Father',$id1);
	$res=$p5->setVal('Mother',$id7);
	$id5 = $p5->save();	
	// Marie Agnes 
	$p7 = new Model($ModN);
	$res=$p7->setVal('Name','Lavency');
	$res=$p7->setVal('SurName','Marie Agnes');
	$res=$p7->setVal('BirthDay','1953-07-18');
	$res=$p7->setVal('Sexe',$s2);
	$res=$p7->setVal('Father',$id3);	
	$id7 = $p7->save();	

	// commit 
$res = $xb->commit();
}

?>