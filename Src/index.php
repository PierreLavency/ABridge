<?php

$path = $home.'/Src'. PATH_SEPARATOR.$home;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'vendor/autoload.php';

use ABridge\ABridge\Controler;

//use ABridge\ABridge\CstError;
//phpinfo();


if (isset($conf['name'])) {
    $application= $conf['name'];
} else {
    throw new exception('No Application Defined');
}


if ($application == 'UnitTest') {
    return;
}

 //try {
    
    $path = "App/".$application .'/';
    require_once $path.'SETUP.php' ;

    $ctrl = new Controler(Config::$config, $conf);
//    $ctrl = new Controler('x', $conf);
    
if (isset($init)) {
    $ctrl->begin();
    require_once $path.'META.php';
    $path = "App/".$application .'/';
    require_once $path.'LOAD.php';
    $ctrl->end();
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
