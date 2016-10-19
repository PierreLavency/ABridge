
<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);
$show = 0;

require_once("PersistFile.php"); 


$test = [
	"ERC" => [0=>["lastId"=>3],1=>["CODE"=> "001", "SEVERITY"=> 1], 2 => ["CODE"=> "002", "SEVERITY"=> 2] ], 
	];

// saving 
if ($show) {print_r($test);echo"<br>";}


$x = new fileBase();
									$line = "x = new fileBase();";
									$z->logLine($line);
$x->objects = $test;
									$line = "x->objects = test;";
									$z->logLine($line);
$r = $x->save();
									$line = "$r = x->save();";
									$z->logLine($line);
$y = new fileBase();
									$line = "y = new fileBase();";
									$z->logLine($line);
$y->load();
									$line = "y->load();";
									$z->logLine($line);

if ($show) {print_r($y->objects);echo "<br/>";}


//select
if ($show) {
	echo "testing select";
	echo "<br/>";
}

$r = implode (' , ',$erc= $x->getObj('ERC',1));
									$line = "$r = implode (' , ',erc= x->getObj('ERC',1));";
									$z->logLine($line);

if ($show) {
print_r($erc);
echo "<br/>";
}

// updating
if ($show){
	echo "testing update";
	echo "<br/>";
}

$erc["SEVERITY"] = 0;
$erc = $y->putObj('ERC',1,$erc);
									$line = "$erc = y->putObj('ERC',1,erc);";
									$z->logLine($line);
$r=$y->save();
									$line = "$r=y->save();";
									$z->logLine($line);

$y = new fileBase();
									$line = "y = new fileBase();";
									$z->logLine($line);
$y->load();
									$line = "y->load();";
									$z->logLine($line);

$r = implode (' , ',$erc= $y->getObj('ERC',1));
									$line = "$r = implode (' , ',erc= y->getObj('ERC',1))";
									$z->logLine($line);
if ($show) {
	print_r($erc);
	echo "<br/>";

	print_r($y->objects);
	echo "<br/>";

// deleting 

	echo "testing delete";
	echo "<br/>";
}

$r=implode(' , ',$erc = $y->getObj('ERC',2));
									$line = "$r=implode(' , ',erc = y->getObj('ERC',2));";
									$z->logLine($line);

if ($show) {	
	print_r($erc);
	echo "<br/>";
}

$r=$y->delObj('ERC',2);
									$line = "$r=y->delObj('ERC',2);";
									$z->logLine($line);


if ($show) {								
	print_r($y->objects);
	echo "<br/>";

	// creating

	echo "testing create";
	echo "<br/>";
}

$id = $y->newObj('ERC',$erc);
									$line = "$id = y->newObj('ERC',erc);";
									$z->logLine($line);


									
if ($show) {	
	print_r($y->objects);
	echo "<br/>";
}
$r=implode(' , ',$erc = $y->getObj('ERC',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERC',$id));";
									$z->logLine($line);

if ($show) {
	print_r($erc);
	echo "<br/>";
}

$r=$y->save();
									$line = "$r=y->save();";
									$z->logLine($line);

$y = new fileBase();
									$line = "y = new fileBase();";
									$z->logLine($line);
$y->load();
									$line = "y->load();";
									$z->logLine($line);

$r=implode(' , ',$erc = $y->getObj('ERC',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERC',$id));";
									$z->logLine($line);

if ($show){

	print_r($erc);
	echo "<br/>";

// new model

	echo "testing new model";
	echo "<br/>";
}

$r=$y->newMod('ERCode');
									$line = "$r=y->newMod('ERCode');";
									$z->logLine($line);

									
if ($show){
	print_r($y->objects);
	echo "<br/>";
}

$id = $y->newObj('ERCode',$erc);
									$line = "$id = y->newObj('ERCode',erc);";
									$z->logLine($line);

$r=implode(' , ',$erc = $y->getObj('ERCode',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERCode',$id));";
									$z->logLine($line);

if ($show){
	print_r($erc);
	echo "<br/>";
}

$r=$y->save();
									$line = "$r=y->save();";
									$z->logLine($line);

$y = new fileBase();									
									$line = "y = new fileBase();";
									$z->logLine($line);

$y->load();
									$line = "y->load();";
									$z->logLine($line);
$r=implode(' , ',$erc = $y->getObj('ERCode',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERCode',$id));";
									$z->logLine($line);
if ($show){
	print_r($erc);
	echo "<br/>";

	echo "testing All values";
	echo "<br/>";


	print_r($y->objects);
	echo "<br/>";
}

$r=implode (' , ',$a = $y->allAttrVal('ERC','CODE'));
									$line = "$r=implode (' , ',a = y->allAttrVal('ERC','CODE'));;";
									$z->logLine($line);
if ($show){
	print_r($a);
	echo "<br/>";
}

$z->save();

?>
