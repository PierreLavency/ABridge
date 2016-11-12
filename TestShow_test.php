
<?php

require_once("Model.php"); 
require_once("View.php"); 

// the test case file
$logName  = 'genealogie'; //'Model_test_5 - Copie';

$db1=getBaseHandler ('fileBase',$logName);
$ModN= 'Code';
$s=initStateHandler ($ModN,'fileBase',$logName);
$classN ='Person';
$s=initStateHandler ($classN,'fileBase',$logName);
	
$classN ='Persons';
$db2=getBaseHandler ('dataBase',$logName);
$s=initStateHandler ($classN,'dataBase',$logName);


if(isset($_SERVER['PATH_INFO'])) {
	$url=$_SERVER['PATH_INFO'];
	$c = pathObj($url);
}
else {
	$c=new Model($classN,$id);
}

$method = $_SERVER['REQUEST_METHOD'];


$v= new View($c);

if (($method =='POST')) {
	$v->postVal();
	if (!$c->isErr()){
		$id = $c->save();		
		if(! $c->isErr()) {
			if ($c->getModName()  == 'Persons') {$r = $db2->commit();}
			else {$r = $db1->commit();}
			if($r) {$method='GET';}
		}		
	}
}

$v->show($method,$c->getId(),true);


?>