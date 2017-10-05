<?php


use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Mod;

class Adm_Prod_Test extends \PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        $classes = [Adm::ADMIN];
        $baseTypes=['dataBase','fileBase','memBase'];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        Mod::reset();
        $res= Adm::reset();
        $this->assertTrue($res);
        
        $mod= Mod::get();
        $adm= Adm::get();
        
        $this->assertNotNull($adm);
        
        $prm['application']['base']='memBase';
        $adm->init($prm['application'], []);
            
        $this->assertEquals('ABridge\ABridge\Adm\Admin', $mod->getClassMod(Adm::ADMIN));
        
        $mod->begin();
        
        $res = Adm::get()->initMeta($prm['application'], []);
        
        $mod->end();
        
        return $prm;
    }
}
