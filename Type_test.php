<?php
	require_once('Type.php');
	
	
	$X = "1";
	$type = M_INT;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 0 ";
	echo "<br/>";
	
	$X=1;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 0 ";
	echo "<br/>";
	
	$X=1.5;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 0 ";
	echo "<br/>";	$X=1;

	$X = "1";
	$type = M_FLOAT;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 0 ";
	echo "<br/>";
	
	$X=1;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 0 ";
	echo "<br/>";
	
	$X=1.5;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 1 ";
	echo "<br/>";	$X=1;
	$X = 0;
	
	$X = "1";
	$type = M_STRING;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 1 ";
	echo "<br/>";
	
	$X=1;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 0 ";
	echo "<br/>";
	
	$X=1.5;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting 0 ";
	echo "<br/>";	$X=1;
	$X = 0;
	

	$X = 0;
	$type = M_BOOL;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting  0";
	echo "<br/>";
	
	$X = "x";
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting  0";
	echo "<br/>";

	$X = "false";
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting  0";
	echo "<br/>";

	$X = 1;
	$r=checkType ($X,$type);
	if ( ! $r ) {$r=0;};
	echo $X ." of type " . $type . " returning ". $r . " expecting  0";
	echo "<br/>";
	
?>	