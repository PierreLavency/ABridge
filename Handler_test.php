

<?php

require_once("Handler.php"); 


$test = [
	"ERC" => [0=>["lastId"=>3],1=>["CODE"=> "001", "SEVERITY"=> 1], 2 => ["CODE"=> "002", "SEVERITY"=> 2] ], 
	];
	
$x = Handler::getInstance();

$y = $x-> getPersist();
$y->objects = $test; 

var_dump($y);
echo "<br/>";


$y = getHandler("file_persist");

var_dump($y);
?>