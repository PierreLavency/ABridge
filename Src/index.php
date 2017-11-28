<?php

$path = $home.'/Src'. PATH_SEPARATOR.$home;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'vendor/autoload.php';

use ABridge\ABridge\Controler;


if (isset($conf['name'])) {
    $application= $conf['name'];
} else {
    throw new exception('No Application Defined');
}


if ($application == 'UnitTest') {
    return;
}

if ($phase=='dev') {
    $fpath= "C:\Users\pierr\ABridge\App\\".$application."\\";
    $conf['fpath']=$fpath;
}


$path = "App/".$application .'/';
require_once $path.'SETUP.php' ;
$config = new Config($conf);

$ctrl = new Controler($config);

if (isset($init)) {
    $ctrl->initMeta();
    $ctrl->close();
    return;
}

$ctrl->run(true);
$ctrl->close();
return;
