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
    
$path = "App/".$application .'/';
require_once $path.'SETUP.php' ;
$config = new Config([], []);

$ctrl = new Controler($config, $conf);

if (isset($init)) {
    $ctrl->begin();
    $config->initMeta();
    $config->initData();
    $ctrl->end();
    $ctrl->close();
    return;
}

$ctrl->run(true);
$ctrl->close();
return;
