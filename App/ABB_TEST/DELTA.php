<?php
	require_once 'CLASSDEC.php';


	$obj = new Model($Role,20);

	
	
	
	
	echo $obj->getId()."<br>";$obj->getErrLog()->show();echo "<br>";
	

	