<?php

use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\AppComp;
use ABridge\ABridge\View\Vew;

class AdmApp_Test_Config extends AppComp
{
    
}

class AdmApp_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        Mod::reset();
        Adm::reset();
        Vew::reset();
        
        $classes = [Adm::ADMIN];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['dataBase']);
        
        $config = new AdmApp_Test_Config([], []);
        $config->setPrm($prm['application']);
        
        $adminApp = new AdmApp($config->getPrm(), $prm['dataBase']);
        
        $mod= Mod::get();
        $adm= Adm::get();
        
        $this->assertfalse($adm->isInit());
        
        $adminApp->init();
       
        $this->assertTrue($adm->isInit());
 
        $mod->begin();
        
        $this->assertEquals([], $adminApp->initData());
        
        $res= $adminApp->initMeta();

        $this->assertEquals($prm['dataBase'], $res);
        
        $mod->end();
    }
}
