<?php

use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\View\View;
use ABridge\ABridge\View\CstView;

function viewCases()
{
    $x=new Model('test');
    $x->deleteMod();

    $x=new Model('test');
    
    $x->addAttr('A1', Mtype::M_INT);

    $x->addAttr('A2', Mtype::M_STRING);

    $x->addAttr('A3', Mtype::M_DATE);

    $x->addAttr('A4', Mtype::M_TXT);

    $x->setVal('A1', 5);

    $x->setVal('A2', 'aa');

    $x->setVal('A3', '2016-12-08');

    $x->setVal('A4', 'ceci est un texte');
    

    $request = new Request('/', CstMode::V_S_READ);
    $handle = new Handle('/', CstMode::V_S_READ, null);
    $v = new View($handle);

    $v->setTopMenu(['/test']);

    $path = '/';
    
    $test = [];
    $test[0]=[$v,$path,CstMode::V_S_CREA,0];

    $path = '/test/1';
    $request = new Request('/test/1', CstMode::V_S_READ);
    $objs=[['test',$x]];
    $handle = new Handle($request, null, $objs, $x, null);
    $v = new View($handle);
    
    $v->setTopMenu(['/test']);

    $v->setAttrList(['A1','A2'], CstView::V_S_REF);
    $v->setLblList(['A1'=>'A1','A2'=>'A2']);
    
    
    $n=1;
    $test[$n]=[$v,$path,CstMode::V_S_CREA,$n];
    $n++;
    $test[$n]=[$v,$path,CstMode::V_S_READ,$n];
    $n++;
    $test[$n]=[$v,$path,CstMode::V_S_UPDT,$n];
    $n++;
    $test[$n]=[$v,$path,CstMode::V_S_DELT,$n];
    $n++;
    $test[$n]=[$v,$path,CstMode::V_S_SLCT,$n];
    $n++;
    $test[$n]=[$v,$path,CstView::V_S_REF,$n];
    $n++;
    $test[$n]=[$v,$path,CstView::V_S_CREF,$n];
    
    return $test;
}
