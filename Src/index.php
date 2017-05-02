<?php

$path = $home.'/Src'. PATH_SEPARATOR .$home;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

//phpinfo();
//$conf['name']='UnitTest';


if (isset($conf['name'])) {
    $application= $conf['name'];
} else {
    throw new exception('No Application Defined');
}

require_once "Controler.php";

if ($application == 'UnitTest') {
    $ctrl = new Controler($conf);
//require_once("Tests/GenHTML_init.php");  
//require_once("Tests/GenJASON_init.php");  
//require_once("Tests/View_init.php");     
//require_once("Tests/View_init_Xref.php"); 
    return;
}


$path = "App/".$application .'/';
require_once $path.'SETUP.php' ;
$ctrl = new Controler($config, $conf);

if (isset($init)) {
    $ctrl->beginTrans();
//    require_once $path.'DELTA.php';
    $ctrl->commit();
    return;
}

//require_once("testAPI.php");

$ctrl->run(true, 0);
return;
