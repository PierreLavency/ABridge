<?php

require_once("Model.php");

function print_result ($i, $x) {
	echo "<br/>";
	echo "example ". $i;
	echo "<br/>";echo "<br/>";
	echo "Attribute";
	echo "<br/>";
	var_dump($x->attr_lst);
	echo "<br/>";
	echo "Type";
	echo "<br/>";
	var_dump($x->attr_typ);
	echo "<br/>";
	echo "Value";
	echo "<br/>";
	var_dump($x->attr_val);
	echo "<br/>";
	};
	
function check_result ($r) {
	if (!$r) {echo "error in Model example ";echo "<br/>";}
	};

$x=new Model();
$r = $x->setAttrList(["name","surname","age","sexe"]);
check_result ($r);

$r = $x->setTyp("name","m_string");
check_result ($r);
$r = $x->setTyp("surname","m_string");
check_result ($r);
$r = $x->setTyp("age","m_int");
check_result ($r);
$r = $x->setTyp("sexe","m_code");
check_result ($r);

$r = $x->setVal("name","Lavency");
check_result ($r);
$r = $x->setVal("surname","Pierre");
check_result ($r);
$r = $x->setVal("age",57);
check_result ($r);
$r = $x->setVal("sexe","M");
check_result ($r);

print_result (1, $x);

$Model_example_1 =$x;

new Model();
$r = $x->setAttrList(["name","surname","age","sexe"]);
if (!$r) {echo "error in Model example";};

$r = $x->setTyp("name","m_string");
check_result ($r);
$r = $x->setTyp("surname","m_string");
check_result ($r);
$r = $x->setTyp("age","m_int");
check_result ($r);
$r = $x->setTyp("sexe","m_code");
check_result ($r);

$r = $x->setVal("name","Arnould");
check_result ($r);
$r = $x->setVal("surname","Dominique");
check_result ($r);
$r = $x->setVal("age",56);
check_result ($r);
$r = $x->setVal("sexe","F");
check_result ($r);

print_result (2, $x);

$Model_example_2 =$x;


?>