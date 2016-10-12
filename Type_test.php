<?php
	
	require_once("UnitTest.php");
	$logName = basename(__FILE__, ".php");
	$z=new unitTest($logName);
	
	require_once('Type.php');
	
	
	$X = '1';
									$line = "$X = '1';";
									$z->logLine($line);
	$type = M_INT;
									$line = "$type = M_INT;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X=1;
									$line = "$X=1;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X=1.5;
									$line = "$X=1.5;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);


	$X = '1';
									$line = "$X = '1';";
									$z->logLine($line);
	$type = M_FLOAT;
									$line = "$type = M_FLOAT;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X=1;
									$line = "$X=1;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X=1.5;
									$line = "$X=1.5;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X = '1';
									$line = "$X = '1';";
									$z->logLine($line);
	$type = M_STRING;
									$line = "$type = M_STRING;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X=1;
									$line = "$X=1;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X=1.5;
									$line = "$X=1.5;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	

	$X = 0;
									$line = "$X = 0;";
									$z->logLine($line);
	$type = M_BOOL;
									$line = "$type = M_BOOL;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	
	$X = 'x';
									$line = "$X = 'x';";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	$X = 'false';
									$line = "$X = 'false';";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	$X = 1;
									$line = "$X = 1;";
									$z->logLine($line);
	$r=checkType ($X,$type);
									$line = "$r=checkType ($X,$type);";
									$z->logLine($line);

	$z->save();
?>	