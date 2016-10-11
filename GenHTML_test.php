<?php

require_once("GenHTML.php"); 

$test1 = ["name"=>"A","default"=>"a1","type"=>"radio","values"=>["a1","a2"], "separator" => "<br/>" ];
$test2 = ["name"=>"A","default"=>"a1","type"=>"text"];
$test3 = ["name"=>"A","default"=>"a1","type"=>"select","values"=>["a1","a2"], "separator" => "<br/>" ];
$test4 = ["name"=>"A","default"=>"a1","type"=>"submit"];
$test5 = ["name"=>"A","default"=>"a1","type"=>"textarea"];
$test6 = ["name"=>"A","default"=>"a1","type"=>"textarea","col"=>50,"row"=>10];
$test7 = ["name"=>"A","default"=>"a1","type"=>"password"];
$test8 = ["plain"=>"this is a text string"];

echo date("d-m-Y H:i:s");
echo NL_O."<br/>".NL_O;
echo date("d-m-Y");
echo NL_O."<br/>".NL_O;;


genFormElem($test1);
echo "<br/>".NL_O;;
genFormElem($test2);
echo "<br/>".NL_O;;
genFormElem($test3);
echo "<br/>".NL_O;;
genFormElem($test4);
echo "<br/>".NL_O;;
genFormElem($test5);
echo "<br/>".NL_O;;
genFormElem($test6);
echo "<br/>".NL_O;;
genFormElem($test7);
echo "<br/>".NL_O;;
genFormElem($test8);
echo "<br/>".NL_O;;

genList([$test1,$test2]);
echo "<br/>".NL_O;;


?>