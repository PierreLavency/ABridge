<?php
//phpinfo();

//require_once("Tests\GenHTML_init.php");  
//require_once("Tests\View_init.php");     
require_once("Tests\View_init_Xref.php");    

$application= 'genealogy';

//require_once("Example\\".$application.'_META.php');
//require_once("Example\\".$application.'_META_1.php');
//require_once("Example\\".$application.'_META_2.php');
//require_once("Example\\".$application.'_META_3.php');
//require_once("Example\\".$application.'_META_4.php');

//require_once("Example\\".$application.'_META_5.php');
//require_once("Example\\" .$application.'_LOAD.php');

//require_once("Example\\".'Person_META.php');

require_once("Example\\" .$application.'_SETUP.php'); // defining config

require_once('controler.php');

$ctrl = new Controler($config);
$ctrl->run(true, 0);



