
<?php

// must clean up interface with set up !!

require_once("Model.php"); 
require_once("View.php"); 

$fb->beginTrans();
$db->beginTrans();

if (isset($_SERVER['PATH_INFO'])) {
    $url=$_SERVER['PATH_INFO'];
    $c = pathObj($url);
    if (!$c) {
        $c=new Model($default, $defaultID);
    }
} else {
    $c=new Model($default, $defaultID);
}

$method = $_SERVER['REQUEST_METHOD'];

$v= new View($c);
$action = 'Get';

if (($method =='POST')) {
    $action = $_POST['action'];
    if ($action == 'Mod' or $action == 'Crt') {
        $v->postVal();
    }
    if (!$c->isErr()) {
        if ($action == 'Del') {
            $c->delet();
        } else {
            $c->save();         
        }
    }
    if (!$c->isErr()) {
        $rf=$fb->commit();  
        $rd=$db->commit();
        if ($rf and $rd) {
            $method='GET';
        }
    } else {
        $rf=$fb->rollback();    
        $rd=$db->rollback();
    }
}

if ($action == 'Del' and $method == 'GET') {
    $c=new Model($default, $defaultID);
    $v= new View($c);
}

$v->show($method, $c->getId(), true);   
