
<?php

require_once("Model.php"); 
require_once("View.php"); 

// the test case file
$DBName  = 'genealogie'; //'Model_test_5 - Copie';

$fb=getBaseHandler ('fileBase',$DBName);
$ModN= 'Code';
$s=initStateHandler ($ModN,'fileBase',$DBName);
$classN ='Person';
$s=initStateHandler ($classN,'fileBase',$DBName);
	
$classN ='Persons';
$db=getBaseHandler ('dataBase',$DBName);
$s=initStateHandler ($classN,'dataBase',$DBName);

$Default 	=$ModN; 
$Default_id =1;

?>