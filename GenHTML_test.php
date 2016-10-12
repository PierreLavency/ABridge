<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);

require_once("GenHTML.php"); 

$test1 = ["name"=>"A","default"=>"a1","type"=>"radio","values"=>["a1","a2"], "separator" => "<br/>" ];
$test2 = ["name"=>"A","default"=>"a1","type"=>"text"];
$test3 = ["name"=>"A","default"=>"a1","type"=>"select","values"=>["a1","a2"], "separator" => "<br/>" ];
$test4 = ["name"=>"A","default"=>"a1","type"=>"submit"];
$test5 = ["name"=>"A","default"=>"a1","type"=>"textarea"];
$test6 = ["name"=>"A","default"=>"a1","type"=>"textarea","col"=>50,"row"=>10];
$test7 = ["name"=>"A","default"=>"a1","type"=>"password"];
$test8 = ["plain"=>"this is a text string"];

$show = false;

$r=genFormElem($test1,$show);
										// logging result
										$xs = "$r=genFormElem(test1,$show);";
										$z->logLine ($xs);
if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test2,$show);			
										// logging result
										$xs = "$r=genFormElem(test2,$show);";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}			
$r=genFormElem($test3,$show);
										// logging result
										$xs = "$r=genFormElem(test3,$show);";
										$z->logLine ($xs);
if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test4,$show);										
										// logging result
										$xs = "$r=genFormElem(test4,$show);";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test5,$show);			
										// logging result
										$xs = "$r=genFormElem(test5,$show);";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test6,$show);		
										// logging result
										$xs = "$r=genFormElem(test6,$show);";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test7,$show);	
										// logging result
										$xs = "$r=genFormElem(test7,$show);";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test8,$show);			
										// logging result
										$xs = "$r=genFormElem(test8,$show);";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}

$r = genList([$test1,$test2],$show);	
										// logging result
										$xs = "$r = genList([test1,test2],$show)";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}

$z->save();
?>