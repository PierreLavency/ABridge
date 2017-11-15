<?php


use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\AppComp;

class Adm_Prod_Config extends AppComp
{
    
}
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
        
        $config = new Admin_Test_Config([], []);
        $config->setPrm($prm['application']);
        
        $adm->init($config->getPrm(), []);
            
        $this->assertEquals('ABridge\ABridge\Adm\Admin', $mod->getClassMod(Adm::ADMIN));
        
        $mod->begin();
        
        $res = Adm::get()->initMeta($prm['application'], []);
        
        $mod->end();
        
        return $prm;
    }
}
