<?php

$path = $home.'/Src'. PATH_SEPARATOR.$home;
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
//require_once("Src/View/Tests/GenHTML_init.php");  
//require_once("Src/View/Tests/GenJASON_init.php");  
//require_once("Src/View/Tests/View_init.php");     
//require_once("Src/View/Tests/View_init_Xref.php"); 
    return;
}

require_once 'CstError.php';

 //try {
    
    $path = "App/".$application .'/';
    require_once $path.'SETUP.php' ;

    $ctrl = new Controler($config, $conf);

if (isset($init)) {
    $ctrl->beginTrans();
    require_once $path.'META.php';
    $path = "App/".$application .'/';
    require_once $path.'LOAD.php';
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
