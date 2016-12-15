
<?php

// must clean up interface with set up !!

require_once("Model.php"); 
require_once("View.php"); 
require_once("Path.php"); 

$fb->beginTrans();
$db->beginTrans();

$level = 0;
$db->setLogLevl($level);
$fb->setLogLevl($level);

$method = $_SERVER['REQUEST_METHOD'];

$path = new Path();
$c = $path->getObj();

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
        foreach ($c->getAllAttr() as $attr) { 
            if ($c->isMdtr($attr) or $c->isOptl($attr)) {
                if (isset($_POST[$attr])) {
                    $val= $_POST[$attr];
                    $typ= $c->getTyp($attr);
                    $valC = convertString($val, $typ);
                    $c->setVal($attr, $valC);
                }
            }
        }
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

if ($actionExec) {
    if ($action == V_S_DELT) {
        $path->pop();
        $c= $path->getObj();
    }
    if ($action == V_S_CREA) {
        $path->pushId($c->getId());
    }
    $action= V_S_READ;
}

$log = $db->getLog();
if ($log) {
    $log->logLine(' **************  ');
}

$v=new View($c);
$v->show($path, $action, true);   

$log = $db->getLog();
if ($log) {
    $log->show();
}
$log = $fb->getLog();
if ($log) {
    $log->show();
    
$db->close();
$fb->close(); 
    

}

