<?php

require_once("Model.php"); 
require_once("View.php"); 

$x=new Model();
echo $x->getVal("id") . " expected 0". "<br/>";
$x->setAttrList(["a1"]);
$x->setVal("a1",1);

$v = new View($x);
$v->show();

?>