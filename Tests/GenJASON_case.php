<?php

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Handler;
use ABridge\ABridge\Mod\Mtype;

function GenJasonCasesData($id, $B, $D)
{
    for ($i=1; $i<$B; $i++) {
        $x = new Model('TestDir');
        $name = 'D_'.$id.'.'.$i;
        $x->setVal('Name', $name);
        $x->setVal('Father', $id);
        $id2= $x->save();
        $x = new Model('TestFle');
        $name = 'F_'.$id.'.'.$i;
        $x->setVal('Name', $name);
        $x->setVal('Father', $id);
        $x->save();
        if ($D > 0) {
            GenJasonCasesData($id2, $B, $D-1);
        }
    }
}

function GenJasonCases()
{
	$prm=[
			'path'=>'C:/Users/pierr/ABridge/Datastore/',
			'host'=>'localhost',
			'user'=>'cl822',
			'pass'=>'cl822'
	];
    $db = Handler::get()->setBase('dataBase', 'test',$prm);
    $db->setLogLevl(0);
    Handler::get()->setStateHandler('TestDir', 'dataBase', 'test');
    Handler::get()->setStateHandler('TestFle', 'dataBase', 'test');
    Handler::get()->setStateHandler('Code', 'dataBase', 'test');
    Handler::get()->setStateHandler('CodeValue', 'dataBase', 'test');
    
    $db->beginTrans();

    // Code
    $Code = 'Code';
    $CodeVal= 'CodeValue';
    
    $codeval = new Model($CodeVal);
    $res= $codeval->deleteMod();
    
    $res = $codeval->addAttr('Name', Mtype::M_STRING);
    $path='/'.$Code;
    $res = $codeval->addAttr('ValueOf', Mtype::M_REF, $path);
    $res = $codeval->saveMod();
    
    $code = new Model($Code);
    $res= $code->deleteMod();
    
    $res = $code->addAttr('Name', Mtype::M_STRING);
    $path='/'.$CodeVal.'/ValueOf';
    $res = $code->addAttr('Values', Mtype::M_CREF, $path);
    $res = $code->saveMod();
    
    $sex = new Model($Code);
    $res = $sex->setVal('Name', 'Sexe');
    $sex_id = $sex->save();
    
    $sex = new Model($CodeVal);
    $res = $sex->setVal('Name', 'Male');
    $res = $sex->setVal('ValueOf', $sex_id);

    $sex_id = $sex->save();
    
    
    $x=new Model('TestDir');
    $x->deleteMod();
    $x->addAttr('Name', Mtype::M_STRING);

    $res = $x->addAttr('Sexe', Mtype::M_CODE, "/Code/1/Values");
    $x->addAttr('Father', Mtype::M_REF, '/TestDir');
    $x->addAttr('FatherOfD', Mtype::M_CREF, '/TestDir/Father');
    $x->addAttr('FatherOfF', Mtype::M_CREF, '/TestFle/Father');
    $x->saveMod();
    $r = $x-> getErrLog();
    $r->show();
        
    $x=new Model('TestFle');
    $x->deleteMod();
    $x->addAttr('Name', Mtype::M_STRING);
    $x->addAttr('Father', Mtype::M_REF, '/TestDir');
    $x->saveMod();
    $r = $x-> getErrLog();
    $r->show();

    $x=new Model('TestDir');
    $x->setVal('Name', '/');
    $x->setVal('Sexe', 1);
    $id = $x->save();
    
    GenJasonCasesData($id, 3, 2);
    $db->Commit();

    $test=[];
    $n=0;
    $test[$n]=['TestDir',1,0,$n];
    $n++;
    $test[$n]=['TestDir',1,1,$n];
    $n++;
    $test[$n]=['TestDir',1,2,$n];
    $n++;
    $test[$n]=['TestDir',1,-1,$n];

    return $test;
}
