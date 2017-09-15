<?php

use ABridge\ABridge\Mod\Model;

use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

function viewCasesXref()
{
	$classes = ['Dir'];
	$baseTypes=['dataBase'];
	$baseType= 'dataBase';
	
	$prm=UtilsC::genPrm($classes, 'View_Xref_Test', $baseTypes);
    
	$dir = $prm[$baseType]['Dir'];
    
    Mod::get()->begin();
    
    $x=new Model($dir);
    $x->deleteMod();

    $x->addAttr('Name', Mtype::M_STRING);
    $x->addAttr('Father', Mtype::M_REF, '/'.$dir);
    $x->addAttr('FatherOf', Mtype::M_CREF, '/'.$dir.'/Father');
    $x->addAttr('Mother', Mtype::M_REF, '/'.$dir);
    $x->addAttr('MotherOf', Mtype::M_CREF, '/'.$dir.'/Mother');
    $x->saveMod();

    $x=new Model($dir);
    $x->setVal('Name', 'Name_1');
    $x->save();

    for ($i=2; $i<10; $i++) {
        $y = new Model($dir);
        $name = 'Name_'.$i;
        $y->setVal('Name', $name);
        $y->setVal('Father', 1);
        $y->setVal('Mother', 1);
        $y->save();
    }

    
    for ($i=2; $i<3; $i++) {
        for ($j=1; $j<12; $j++) {
            $z= new Model($dir);
            $n = (10*$i)+$j;
            $name = 'Name_'.$n;
            $z->setVal('Name', $name);
            $z->setVal('Father', $i);
            $z->setVal('Mother', $i);
            $z->save();
        }
    }

   
    Mod::get()->End();

    $v = 2;
    $path = '/'.$dir.'/'.$v;
    
    $test=[];
    $n=0;
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
