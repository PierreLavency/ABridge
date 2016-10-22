<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$z=new unitTest($logName);

$show = true;

$log->logLine('/* standalone test */');
	
/**************************************/

require_once("GenHTML.php"); 

$test1 = [H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_RADIO,"values"=>["a1","a2"], "separator" => "<br/>" ];
$test2 = [H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_TEXT];
$test3 = [H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_SELECT,"values"=>["a1","a2"], "separator" => "<br/>" ];
$test4 = [H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_SUBMIT];
$test5 = [H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_TEXTAREA];
$test6 = [H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_TEXTAREA,H_COL=>50,H_ROW=>10];
$test7 = [H_NAME=>"A",H_DEFAULT=>"a1",H_TYPE=>H_T_PASSWORD];
$test8 = [H_TYPE=>H_T_PLAIN,H_DEFAULT=>"this is a text string"];
$test8a =[H_TYPE=>H_T_PLAIN,H_DEFAULT=>"this is another text string"];
$test9 = [$test1,$test2];
$test10 = [H_TYPE=>H_T_LIST,H_ARG=>[$test1,$test2]];
$test11 = [H_TYPE=>H_T_LIST,H_ARG=>[[H_TYPE=>H_T_LIST,H_ARG=>[$test8,$test8]],$test8a]];
$test12 = [H_TYPE=>H_T_LIST,H_ARG=>[[H_TYPE=>H_T_LIST,H_ARG=>[$test8,$test8]],[H_TYPE=>H_T_LIST,H_ARG=>[$test8a,$test8a]]]];

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


$r1 = genList($test9,$show);
	
										// logging result
										$xs = "$r1 = genList(test9,$show)";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}

$r=genFormElem($test10,$show);			
										// logging result
										$xs = "$r=genFormElem(test10,$show);";
										$z->logLine ($xs);

if($show) {echo "<br/>".NL_O;}

$x = ($r1 == $r); 
										// logging result
										$xs = "$x = (r1 == r); ";
										$z->logLine ($xs);
										

$r=genFormElem($test11,$show);
										// logging result
										$xs = "$r=genFormElem(test11,$show);";
										$z->logLine ($xs);
$r=genFormElem($test12,$show);
										// logging result
										$xs = "$r=genFormElem(test12,$show);";
										$z->logLine ($xs);
$z->saveTest();
?>