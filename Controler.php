
<?php

// must clean up interface with set up !!

require_once("Model.php"); 
require_once("View.php"); 

$fb->beginTrans();
$db->beginTrans();

if(isset($_SERVER['PATH_INFO'])) {
	$url=$_SERVER['PATH_INFO'];
	$c = pathObj($url);
	if (!$c) {$c=new Model($Default,$Default_id);}
}
else {
	$c=new Model($Default,$Default_id);
}

$method = $_SERVER['REQUEST_METHOD'];

$v= new View($c);
$action = 'Get';

if (($method =='POST')) {
	$action = $_POST['action'];
	if ($action == 'Mod' or $action == 'Crt') {
		$v->postVal();
	}
	if (!$c->isErr()){
		if ($action == 'Del') {
			$c->delet();
		}
		else {
			$c->save();			
		}
	}
	if (!$c->isErr()) {
		$r1=$fb->commit();	
		$r2=$db->commit();
		if($r1 and $r2) {$method='GET';}
	}		
	else {
		$r1=$fb->rollback();	
		$r2=$db->rollback();
	}
}

if ($action == 'Del' and $method == 'GET') {
	$c=new Model($Default,$Default_id);
	$v= new View($c);
}

$v->show($method,$c->getId(),true);	

?>