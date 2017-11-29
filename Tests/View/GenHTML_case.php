<?php
use ABridge\ABridge\View\CstHTML;

function GenHTLMCases()
{
        
    $test=[];
    $n=0;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_RADIO,   CstHTML::H_NAME=>'A',CstHTML::H_DEFAULT=>"a1",CstHTML::H_VALUES=>[['a1','a1'],['a2','a2']], CstHTML::H_SEPARATOR => "<br/>" ],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_TEXT,    CstHTML::H_NAME=>'A',CstHTML::H_DEFAULT=>"a1"],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_SELECT,  CstHTML::H_NAME=>'A',CstHTML::H_DEFAULT=>"a1",CstHTML::H_VALUES=>[['a1','a1'],['a2','a2']]],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_SUBMIT              ,CstHTML::H_LABEL=>'Submit'],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_SUBMIT              ,CstHTML::H_LABEL=>'Submit',CstHTML::H_BACTION=>'ABridge.php/code/1'],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_NAME=>'A',CstHTML::H_DEFAULT=>"a1"],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_NAME=>'A',CstHTML::H_DEFAULT=>"a1",CstHTML::H_COL=>50,CstHTML::H_ROW=>10,CstHTML::H_DISABLED=>true],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_PASSWORD,CstHTML::H_NAME=>'A',CstHTML::H_DEFAULT=>"a1"],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_PLAIN,               CstHTML::H_DEFAULT=>"this is a text string"],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_PLAIN,               CstHTML::H_DEFAULT=>"this is another text string"],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_LINK,    CstHTML::H_NAME=>'ABridge.php/code/1',CstHTML::H_LABEL=>'testSuite'],$n];

    //n=10;

    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_TABLE ,CstHTML::H_ARG=>[$test[0][0],$test[1][0]]],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_LIST  ,CstHTML::H_ARG=>[$test[0][0],$test[1][0]]],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_LIST_BR  ,CstHTML::H_ARG=>[$test[0][0],$test[1][0]]],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_CONCAT  ,CstHTML::H_ARG=>[$test[1][0],$test[1][0]]],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_LIST  ,CstHTML::H_ARG=>[[CstHTML::H_TYPE=>CstHTML::H_T_LIST,CstHTML::H_ARG=>[$test[7][0],$test[8][0]]]]],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_TABLE ,CstHTML::H_ARG=>[[CstHTML::H_TYPE=>CstHTML::H_T_LIST,CstHTML::H_ARG=>[$test[7][0],$test[8][0]]]]],$n];
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_LIST ,CstHTML::H_ARG=>[[CstHTML::H_TYPE=>CstHTML::H_T_LIST,CstHTML::H_ARG=>[$test[7][0],$test[7][0]]],
                    [CstHTML::H_TYPE=>CstHTML::H_T_LIST,CstHTML::H_ARG=>[$test[8][0],$test[8][0]]]]],$n];

    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_FORM ,CstHTML::H_ACTION=>'POST',CstHTML::H_HIDDEN=>['Action'=>'toto'],CstHTML::H_URL=>'testSuite',CstHTML::H_ARG=>[$test[2][0],$test[3][0]]],$n];

    $n++;
    $test[$n] = [[CstHTML::H_NAME=>'A',CstHTML::H_DEFAULT=>"a1"],$n];
    
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,CstHTML::H_TABLEN=>2,CstHTML::H_ARG=>[$test[1][0],$test[1][0],$test[1][0]]],$n];

    $n++;
    $test[$n] = [[CstHTML::H_DIV=>'header', CstHTML::H_TYPE=>CstHTML::H_T_PLAIN,               CstHTML::H_DEFAULT=>"this is a text string in a div"],$n];
    $n++;
    $test[$n] = [[CstHTML::H_DIV_S=>'header', CstHTML::H_DIV_E=>'header',CstHTML::H_TYPE=>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>"this is a text string in a div S/E"],$n];
    
    $n++;
    $test[$n] = [[CstHTML::H_TYPE=>CstHTML::H_T_IMG, CstHTML::H_DEFAULT=>"/pl.jpg",CstHTML::H_COLP=>100,CstHTML::H_ROWP=>100],$n];
    return $test;
}
