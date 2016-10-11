<?php

require_once("Model.php"); 
require_once("View.php"); 

$x=new Model();
$x->setAttrList(["a1"]);
$x->setVal("a1",1);

$v = new View($x);
$v->show();

?>