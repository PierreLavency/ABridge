
<?php
	require_once("UnitTest.php");
	$logName = basename(__FILE__, ".php");
	$z=new unitTest($logName);

	require_once("Handler.php"); 
	$show = false;

	$test = [
		"ERC" => [0=>["lastId"=>3],1=>["CODE"=> "001", "SEVERITY"=> 1], 2 => ["CODE"=> "002", "SEVERITY"=> 2] ], 
		];
		
	$x = Handler::getInstance();
									$line = "x = Handler::getInstance();";
									$z->logLine($line);
	$y = $x-> getFileBase();
									$line = "y = x-> getFileBase();";
									$z->logLine($line);
	$y->objects = $test; 

	if ($show){
		var_dump($y);
		echo "<br/>";
	}

	$s = getHandler('file_persist');
									$line = "s = getHandler('file_persist');";
									$z->logLine($line);
	
	$r = ($y === $s);
									$line = "	$r = (y === s);";
									$z->logLine($line);


	if ($show){
		var_dump($r);
	}
	
	
	$z->save();
?>