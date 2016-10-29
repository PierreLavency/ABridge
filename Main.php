<?php

$url = "";

if(isset($_SERVER['PATH_INFO'])) {
	$url=$_SERVER['PATH_INFO'];
	echo 'url is '.$url. '<br>';
}
else {
	echo 'url is null'. '<br>';
}

require_once("TestSuite_test.php"); 


?>