<?php


function GenHTLMCases()

{
		
	$test=[];
	$n=0;
	$test[$n] = [[H_TYPE=>H_T_RADIO,   H_NAME=>'A',H_DEFAULT=>"a1",H_VALUES=>[['a1','a1'],['a2','a2']], H_SEPARATOR => "<br/>" ],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_TEXT,    H_NAME=>'A',H_DEFAULT=>"a1"],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_SELECT,  H_NAME=>'A',H_DEFAULT=>"a1",H_VALUES=>[['a1','a1'],['a2','a2']]],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_SUBMIT              ,H_LABEL=>'Submit'],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_TEXTAREA,H_NAME=>'A',H_DEFAULT=>"a1"],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_TEXTAREA,H_NAME=>'A',H_DEFAULT=>"a1",H_COL=>50,H_ROW=>10,H_DISABLED=>true],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_PASSWORD,H_NAME=>'A',H_DEFAULT=>"a1"],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_PLAIN,               H_DEFAULT=>"this is a text string"],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_PLAIN,               H_DEFAULT=>"this is another text string"],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_LINK,    H_NAME=>'ABridge.php/code/1',H_LABEL=>'testSuite'],$n];

	//n=10;

	$n++;
	$test[$n] = [[H_TYPE=>H_T_TABLE ,H_ARG=>[$test[0][0],$test[1][0]]],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_LIST  ,H_ARG=>[$test[0][0],$test[1][0]]],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_LIST_BR  ,H_ARG=>[$test[0][0],$test[1][0]]],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_CONCAT  ,H_ARG=>[$test[1][0],$test[1][0]]],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_LIST  ,H_ARG=>[[H_TYPE=>H_T_LIST,H_ARG=>[$test[7][0],$test[8][0]]]]],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_TABLE ,H_ARG=>[[H_TYPE=>H_T_LIST,H_ARG=>[$test[7][0],$test[8][0]]]]],$n];
	$n++;
	$test[$n] = [[H_TYPE=>H_T_LIST ,H_ARG=>[[H_TYPE=>H_T_LIST,H_ARG=>[$test[7][0],$test[7][0]]],
					[H_TYPE=>H_T_LIST,H_ARG=>[$test[8][0],$test[8][0]]]]],$n];

	$n++;
	$test[$n] = [[H_TYPE=>H_T_FORM ,H_ACTION=>'POST',H_HIDDEN=>'toto',H_URL=>'testSuite',H_ARG=>[$test[2][0],$test[3][0]]],$n];

	$n++;
	$test[$n] = [[H_NAME=>'A',H_DEFAULT=>"a1"],$n];
	
	return $test;
}


