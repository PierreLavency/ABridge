
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

if (($method =='POST')) {
	$v->postVal();
	if (!$c->isErr()){$id=$c->save();}
	if(! $c->isErr()) {
		$r1=$fb->commit();	
		$r2=$db->commit();
		if($r1 and $r2) {$method='GET';}
	}		
	else {
		$r1=$fb->rollback();	
		$r2=$db->rollback();
	}
}

$v->show($method,$c->getId(),true);

?>