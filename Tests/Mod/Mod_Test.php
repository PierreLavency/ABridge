<?php

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

class Mod_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $prm=UtilsC::genPrm(['test1','test2','test3','test4'], get_called_class());

        $config=[
                'test1'=>[],
                'test2'=>['dataBase'],
                'test3'=>['fileBase'],
                'test4'=>['dataBase','test'],
        ];
        
        Mod::get()->reset();
        $mod= Mod::get();
        
        $mod->init($prm['application'], $config);
        $this->assertTrue($mod->isNew());
        $this->assertEquals(['test1','test2','test3','test4'], $mod->getMods());
                
        
        Mod::get()->reset();
        $mod= Mod::get();
                
        $mod->init($prm['application'], $prm['handlers']);

        $this->assertEquals(8, count($mod->getMods()));
        
        $mod->begin();
        
        foreach ($prm['names'] as $modName) {
            $obj=new Model($modName);
            $obj->deleteMod();
            $obj->addAttr('Name', Mtype::M_STRING);
            $res=$obj->saveMod();
            $this->assertTrue($res);
        }
        
        $res=$mod->end();
        $this->assertTrue($res);

        $mod->begin();
        foreach ($prm['names'] as $modName) {
            $obj=new Model($modName);
            $obj->setVal('Name', 'toto');
            $res=$obj->save();
            $this->assertEquals(1, $res);
        }
        
        $res=$mod->end();
        $this->assertTrue($res);
    }
}
