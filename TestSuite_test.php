<?php
//phpinfo();

//require_once("Tests\GenHTML_init.php");  
//require_once("Tests\View_init.php");     
//require_once("Tests\View_init_Xref.php");    

$application= 'genealogy';

//require_once("Example\\".'ABB_META.php');
//require_once("Example\\".'Acode_META.php');
//require_once("Example\\".'User_META.php');

//require_once("Example\\" .$application.'_META.php');
//require_once("Example\\" .$application.'_LOAD.php');

require_once('controler.php');

require_once("Example\\" .$application.'_SETUP.php'); // defining config


//require_once("testAPI.php");

$ctrl = new Controler($config);
$ctrl->run(true, 0);


