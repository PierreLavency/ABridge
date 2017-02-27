<?php

$path = $home.'/Src'. PATH_SEPARATOR .$home;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

//phpinfo();
//require_once("Tests/GenHTML_init.php");  
//require_once("Tests/View_init.php");     
//require_once("Tests/View_init_Xref.php");    

$run = false; 
if (isset($conf['name'])) {
    $application= $conf['name'];
    if ($application != 'UnitTest') {
        $run = true;
    }
}
require_once "Controler.php";
if ($run) { 
    $path = $application . '_SETUP.php';
    require_once("App/".$application.'/' .$application.'_SETUP.php');

    //require_once("testAPI.php");

    $ctrl = new Controler($config, $conf);
    $ctrl->run(true, 0);
} else {
    $ctrl = new Controler($conf);
}
