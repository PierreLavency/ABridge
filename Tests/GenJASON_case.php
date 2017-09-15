<?php


use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Mtype;

function GenJasonCasesData($testDir, $testFile, $id, $B, $D)
{
    for ($i=1; $i<$B; $i++) {
        $x = new Model($testDir);
        $name = 'D_'.$id.'.'.$i;
        $x->setVal('Name', $name);
        $x->setVal('Father', $id);
        $id2= $x->save();
        $x = new Model($testFile);
        $name = 'F_'.$id.'.'.$i;
        $x->setVal('Name', $name);
        $x->setVal('Father', $id);
        $x->save();
        if ($D > 0) {
            GenJasonCasesData($testDir, $testFile, $id2, $B, $D-1);
        }
    }
}

function GenJasonCases()
{
	$classes = ['testDir','testFile','CodeVal','Code'];
	$baseTypes=['dataBase'];
	$baseType='dataBase';
	
	$prm=UtilsC::genPrm($classes, 'GENJASON_Test', $baseTypes);
	Mod::get()->init($prm['application'], $prm['handlers']);
	
	$baseType='dataBase';
    $Code = $prm[$baseType]['Code'];
    $CodeVal= $prm[$baseType]['CodeVal'];
    $testDir= $prm[$baseType]['testDir'];
    $testFile=$prm[$baseType]['testFile'];
    
    
    Mod::get()->begin();

    
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
    
    
    $x=new Model($testDir);
    $x->deleteMod();
    $x->addAttr('Name', Mtype::M_STRING);

    $res = $x->addAttr('Sexe', Mtype::M_CODE, '/'.$Code.'/1/Values');
    $x->addAttr('Father', Mtype::M_REF, '/'.$testDir);
    $x->addAttr('FatherOfD', Mtype::M_CREF, '/'.$testDir.'/Father');
    $x->addAttr('FatherOfF', Mtype::M_CREF, '/'.$testFile.'/Father');
    $x->saveMod();
    $r = $x-> getErrLog();
    $r->show();
        
    $x=new Model($testFile);
    $x->deleteMod();
    $x->addAttr('Name', Mtype::M_STRING);
    $x->addAttr('Father', Mtype::M_REF, '/'.$testDir);
    $x->saveMod();
    $r = $x-> getErrLog();
    $r->show();

    $x=new Model($testDir);
    $x->setVal('Name', '/');
    $x->setVal('Sexe', 1);
    $id = $x->save();
    
    GenJasonCasesData($testDir, $testFile, $id, 3, 2);
    Mod::get()->End();
    

    $test=[];
    $n=0;
    $test[$n]=[$testDir,1,0,$n];
    $n++;
    $test[$n]=[$testDir,1,1,$n];
    $n++;
    $test[$n]=[$testDir,1,2,$n];
    $n++;
    $test[$n]=[$testDir,1,-1,$n];

    return $test;
}
