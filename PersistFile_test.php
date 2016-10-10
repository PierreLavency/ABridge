
<?php

require_once("PersistFile.php"); 


$test = [
	"ERC" => [0=>["lastId"=>3],1=>["CODE"=> "001", "SEVERITY"=> 1], 2 => ["CODE"=> "002", "SEVERITY"=> 2] ], 
	];

// saving 

echo "testing save";
echo "<br/>";

print_r($test);
echo "<br/>";

$x = new file_persist();
$x->objects = $test;
$x->save();

$y = new file_persist();
$y->load();
print_r($y->objects);
echo "<br/>";

//select

echo "testing select";
echo "<br/>";


$erc = $x->getObj("ERC",1);
print_r($erc);
echo "<br/>";


// updating

echo "testing update";
echo "<br/>";

$erc["SEVERITY"] = 0;
$erc = $y->putObj("ERC",1,$erc);

$y->save();
$y = new file_persist();
$y->load();
$erc = $y->getObj("ERC",1);
print_r($erc);
echo "<br/>";

print_r($y->objects);
echo "<br/>";

// deleting 

echo "testing delete";
echo "<br/>";

$erc = $y->getObj("ERC",2);
print_r($erc);
echo "<br/>";

$y->delObj("ERC",2);
print_r($y->objects);
echo "<br/>";

// creating

echo "testing create";
echo "<br/>";

$id = $y->newObj("ERC",$erc);
print_r($y->objects);
echo "<br/>";
$erc = $y->getObj("ERC",$id);
print_r($erc);
echo "<br/>";

$y->save();

$y = new file_persist();
$y->load();
$erc = $y->getObj("ERC",$id);
print_r($erc);
echo "<br/>";

// new model

echo "testing new model";
echo "<br/>";

$y->newMod("ERCode");
print_r($y->objects);
echo "<br/>";

$id = $y->newObj("ERCode",$erc);

$erc = $y->getObj("ERCode",$id);
print_r($erc);
echo "<br/>";

$y->save();

$y = new file_persist();
$y->load();
$erc = $y->getObj("ERCode",$id);
print_r($erc);
echo "<br/>";

echo "testing All values";
echo "<br/>";

print_r($y->objects);
echo "<br/>";

$a = $y->allAttrVal("ERC","CODE");
print_r($a);
echo "<br/>";




?>
