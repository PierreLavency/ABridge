<?php

use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class AdmApp_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        $classes = [Adm::ADMIN];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['dataBase']);
        
        $res = AdmApp::init($prm, $prm['dataBase']);
        
        $this->assertEquals($prm['dataBase'], $res['Adm']);
        
        $this->assertTrue(AdmApp::initData());
        
        
        Mod::reset();
        Adm::reset();

        
        $mod= Mod::get();
        $adm= Adm::get();
        
        $adm->init($prm['application'], $prm['dataBase']);
        
        
        $mod->begin();
        
        $res= AdmApp::initMeta([]);
        $this->assertEquals($prm['dataBase'], $res);
        
        $mod->end();
    }
}
