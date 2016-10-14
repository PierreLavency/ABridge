<?php

require_once("Model.php"); 
require_once("View.php"); 

$x=new Model();
$x->setAttrList(['a1']);
$x->setVal('a1',1);

$v = new View($x);
$v->attr_lbl = array('id'=>'object reference','vnum'=>'version number','ctstp'=>'creation time stamp');
$v->show();

$r=$v->viewAttr('id',V_P_LBL,[]);
var_dump($r);
$er = ['plain'=>'object reference'];
echo $er === $r;

$r=$v->viewAttr('vnum',V_P_LBL);
var_dump($r);
$er = ['plain'=>'version number'];
echo $er === $r;

$r=$v->viewAttr('a1',V_P_ATTR,[H_TYPE=>H_T_TEXT]);
var_dump($r);
$er = [H_TYPE=>H_T_TEXT,H_NAME=>'a1',"default"=>1];
echo $er === $r;

$r



?>