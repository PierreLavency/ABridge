
<?php

// must clean up interface with set up !!

require_once("Model.php"); 
require_once("View.php"); 

$fb->beginTrans();
$db->beginTrans();

$level = 1;
$db->setLogLevl($level);
$fb->setLogLevl($level);

if (isset($_SERVER['PATH_INFO'])) {
    $url=$_SERVER['PATH_INFO'];
    $c = pathObj($url);
    if (!$c) {
        $c=new Model($default, $defaultId);
    }
} else {
    $c=new Model($default, $defaultId);
}

$method = $_SERVER['REQUEST_METHOD'];

$v= new View($c);
$action = V_S_READ;
$actionExec = false;

if ($method == 'GET') {
    if (isset($_GET['View'])) {
        $action = $_GET['View'];
    }
    if (! $c->getid()) {
        $action = V_S_CREA;
    }
}
if ($method =='POST') {
    $action = $_POST['action'];
    if ($action == V_S_UPDT or $action == V_S_CREA) {
        $v->postVal();
    }
    if (!$c->isErr()) {
        if ($action == V_S_DELT) {
            $c->delet();
        }
        if ($action == V_S_UPDT or $action == V_S_CREA) {
            $c->save();         
        }
    }
    if (!$c->isErr()) {
        $rf=$fb->commit();  
        $rd=$db->commit();
        if ($rf and $rd) {
            $actionExec=true;
        }
    } else {
        
        $rf=$fb->rollback();    
        $rd=$db->rollback();
    }
}

if ($action == V_S_DELT and $actionExec) {
    $c=new Model($default, $defaultId);
    $v=new View($c);
}

if ($actionExec) {
    $action= V_S_READ;
}
$v->show($action, true);   

$log = $db->getLog();
if ($log) {
    $log->show();
}
$log = $fb->getLog();
if ($log) {
    $log->show();
}

