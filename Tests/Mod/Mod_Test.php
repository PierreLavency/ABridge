<?php

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Handler;

class Mod_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $prm=[
                'base'=>'dataBase',
                'dbnm'=>'test',
                'flnm'=>'test',
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];
        $config=[
                'test1'=>[],
                'test2'=>['dataBase'],
                'test3'=>['fileBase'],
                'test4'=>['dataBase','test'],
        ];
        Mod::get()->reset();
        $mod= Mod::get();
        $mod->init($prm, $config);
        $this->assertTrue($mod->isNew());
        $this->assertEquals(['test1','test2','test3','test4'], $mod->getMods());
    }
}
