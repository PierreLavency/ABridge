<?php

require_once("Request.php");
require_once("SessionHdl.php");
require_once("Handle.php");
require_once("Model.php");
require_once("View.php");

function viewCases()
{
    $x=new Model('test');
    $x->deleteMod();

    $x=new Model('test');
    
    $x->addAttr('A1', M_INT);

    $x->addAttr('A2', M_STRING);

    $x->addAttr('A3', M_DATE);

    $x->addAttr('A4', M_TXT);

    $x->setVal('A1', 5);

    $x->setVal('A2', 'aa');

    $x->setVal('A3', '2016-12-08');

    $x->setVal('A4', 'ceci est un texte');
    

    $home= new sessionHdl();
    $request = new Request('/', V_S_READ);
    $handle = new Handle('/', V_S_READ, $home);
    $v = new View($handle);

    $v->setTopMenu(['/test']);

    $path = '/';
    
    $test = [];
    $test[0]=[$v,$path,V_S_CREA,0];

    $path = '/test/1';
    $request = new Request('/test/1', V_S_READ);
    $objs=[['test',$x]];
    $handle = new Handle($request, $home, $objs, $x, null);
    $v = new View($handle);
    
    $v->setTopMenu(['/test']);

    $v->setAttrList(['A1','A2'], V_S_REF);
    $v->setLblList(['A1'=>'A1','A2'=>'A2']);
    
    
    $n=1;
    $test[$n]=[$v,$path,V_S_CREA,$n];
    $n++;
    $test[$n]=[$v,$path,V_S_READ,$n];
    $n++;
    $test[$n]=[$v,$path,V_S_UPDT,$n];
    $n++;
    $test[$n]=[$v,$path,V_S_DELT,$n];
    $n++;
    $test[$n]=[$v,$path,V_S_SLCT,$n];
    $n++;
    $test[$n]=[$v,$path,V_S_REF,$n];
    $n++;
    $test[$n]=[$v,$path,V_S_CREF,$n];
    
    return $test;
}
