<?php
$home = "C:\Users\pierr\ABridge";

$path = $home.'\Src'. PATH_SEPARATOR .$home;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once("TestSuite_test.php"); 

//phpinfo();

//require_once("Tests\GenHTML_init.php");  
//require_once("Tests\View_init.php");     
//require_once("Tests\View_init_Xref.php");    

$config = parse_ini_file("config.ini");
$application= $config['name'];

require_once('controler.php');
require_once("App\\" .$application.'_SETUP.php'); // defining config

//require_once("testAPI.php");

$ctrl = new Controler($config);
$ctrl->run(true, 0);

