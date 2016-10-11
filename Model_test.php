<?php

require_once("Model.php"); 


$x=new Model();

echo "Model name:  " . $x->name ."<br>";

try {
	$x=new Model(1);
	}
catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "<br>";
}


$x=new Model('Test');

echo "Model name:  " . $x->name ."<br>";

echo $x->getVal("id") . " expected 0". "<br/>";
$x->setAttrList(["y"]);
//var_dump($x);
//echo "<br/>";
$x->setAttrList(["a1"]);
$x->setVal("a1",1);
//var_dump($x);
//echo "<br/>"; 
echo "id: ". $x->existsAttr("id") . " -> id" . "<br/>";
echo "a1: ". $x->existsAttr("a1") . " -> a1" . "<br/>";
echo "a2: ". $x->existsAttr("a2") . " -> 0" . "<br/>";
echo "a1: ". $x->setVal("a1",2)   . " -> 2" . "<br/>";
echo "id: ". $x->setVal("id",2)   . " -> 0" . "<br/>";
echo "a1: ". $x->getVal("a1")     . " -> 2" . "<br/>";
echo "id: ". $x->getVal("id")     . " -> 0" . "<br/>";
echo "a2: ". $x->setVal("a2",3)   . " -> 0" . "<br/>";
$x->setAttrList(["a1","a2"]);
echo "a2: ". $x->setVal("a2",3)   . " -> 3" . "<br/>";
echo "* : ". print_r($x->getAttrList())    . " -> id, vnum ctstp utstp a1 a2" . "<br/>";
print_r($x->attr_val);

?>