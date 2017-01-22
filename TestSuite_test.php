<?php
//phpinfo();

//require_once("Tests\GenHTML_init.php");  
//require_once("Tests\View_init.php");     
//require_once("Tests\View_init_Xref.php");    

$application= 'genealogy';

//require_once("Example\\".'ABB_META.php');
//require_once("Example\\".'Acode_META.php');
//require_once("Example\\".'User_META.php');

//require_once("Example\\" .$application.'_META_7.php');

require_once("Example\\" .$application.'_SETUP.php'); // defining config

require_once('controler.php');

$ctrl = new Controler($config);
$ctrl->run(true, 0);



