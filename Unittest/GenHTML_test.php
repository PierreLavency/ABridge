<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);


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
$test13 = [H_TYPE=>H_T_LINK,H_NAME=>'ABridge.php/code/1',H_LABEL=>'testSuite'];
$test14 = [H_TYPE=>H_T_FORM,H_ACTION=>'POST',H_URL=>'testSuite',H_ARG=>[$test2,$test4]];

$show = false;
if($show) {echo "<br/>".NL_O;}

$r=genFormElem($test1,$show);

	$line = "$r=genFormElem(test1,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test2,$show);			

	$line = "$r=genFormElem(test2,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}			
$r=genFormElem($test3,$show);

	$line = "$r=genFormElem(test3,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test4,$show);				
						
	$line = "$r=genFormElem(test4,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test5,$show);			

	$line = "$r=genFormElem(test5,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test6,$show);		

	$line = "$r=genFormElem(test6,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test7,$show);	

	$line = "$r=genFormElem(test7,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}
$r=genFormElem($test8,$show);			

	$line = "$r=genFormElem(test8,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}


$r1 = genList($test9,$show);
	
	$line = "$r1 = genList(test9,$show)";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}

$r=genFormElem($test10,$show);			

	$line = "$r=genFormElem(test10,$show);";$log->logLine ($line);

if($show) {echo "<br/>".NL_O;}

$x = ($r1 == $r); 

	$line = "$x = (r1 == r); ";$log->logLine ($line);

$r=genFormElem($test11,$show);

	$line = "$r=genFormElem(test11,$show);";$log->logLine ($line);

$r=genFormElem($test12,$show);

	$line = "$r=genFormElem(test12,$show);";$log->logLine ($line);

$r=genFormElem($test13,$show);

	$line = "$r=genFormElem(test13,$show);";$log->logLine ($line);
	
$r=genFormElem($test14,$show);

	$line = "$r=genFormElem(test14,$show);";$log->logLine ($line);
	
if($show) {echo "<br/>".NL_O;}
	
$log->saveTest();

//$log->showTest();

?>