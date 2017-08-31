<?php

$path = $home.'/Src'. PATH_SEPARATOR.$home;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'vendor/autoload.php';

use ABridge\ABridge\Controler;

//use ABridge\ABridge\CstError;

//phpinfo();
//$conf['name']='UnitTest';

if (isset($conf['name'])) {
    $application= $conf['name'];
} else {
    throw new exception('No Application Defined');
}


if ($application == 'UnitTest') {
//	var_dump($conf);
    $ctrl = new Controler($conf);
//require_once("Tests/View/GenHTML_init.php");  
//require_once("Tests/GenJASON_init.php");  
//require_once("Tests/View/View_init.php");     
//require_once("Tests/View/View_init_Xref.php"); 
    return;
}


 //try {
    
    $path = "App/".$application .'/';
    require_once $path.'SETUP.php' ;

    $ctrl = new Controler(Config::$config, $conf);

if (isset($init)) {
    $ctrl->beginTrans();
    require_once $path.'META.php';
    $path = "App/".$application .'/';
//    require_once $path.'LOAD.php';
    $ctrl->commit();
    $ctrl->close();
    return;
}

    //require_once("testAPI.php");

    $ctrl->run(true, 0);
    $ctrl->close();
    return;
/*    
} catch (Exception $e) {
    $mes = CstError::subst($e->getmessage());
    throw new Exception($mes);
}
*/
