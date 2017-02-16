<?php

$home = "phar://ABridge.phar";
$path = $home.'\Src'. PATH_SEPARATOR .$home;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

$application= $conf['name'];

require_once "Controler.php";

$path = $application . '_SETUP.php';
require_once("App\\" .$application.'_SETUP.php');

$ctrl = new Controler($config);
$ctrl->run(true, 0);
