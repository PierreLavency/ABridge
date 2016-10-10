<?php
require_once("Model.php"); 
require_once("GenHTML.php"); 

$test1 = ["type"=>"radio","values"=>["a1","a2"], "separator" => "<br/>" ];
$test2 = ["type"=>"text"];
$test3 = ["type"=>"select","values"=>["a1","a2"], "separator" => "<br/>" ];
$test4 = ["type"=>"submit"];
$test5 = ["type"=>"textarea"];
$test6 = ["type"=>"textarea","col"=>50,"row"=>10];
$test7 = ["type"=>"password"];


$x=new Model();
$x->setAttrList(["A"]);
$x->setVal("A","a2");


echo date("d-m-Y H:i:s");
echo "<br/>";
echo date("d-m-Y");
echo "<br/>";

displayAttr($x, "A", $test1);
echo "<br/>";
displayAttr($x, "A", $test2);
echo "<br/>";
displayAttr($x, "A", $test3);
echo "<br/>";
displayAttr($x, "A", $test4);
echo "<br/>";
displayAttr($x, "A", $test5);
echo "<br/>";
displayAttr($x, "A", $test6);
echo "<br/>";
displayAttr($x, "A", $test7);
echo "<br/>";



?>