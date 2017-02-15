<?php
require_once "phar://ABridge.phar/controler.php";

$conf = parse_ini_file("config.ini");
$application= $conf['name'];
$path = 'Example'. PATH_SEPARATOR . $application . '_SETUP.php';
require_once "phar://ABridge.phar/$path";

$ctrl = new Controler($config);
$ctrl->run(true, 0);
