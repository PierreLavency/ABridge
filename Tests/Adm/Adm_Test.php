<?php

use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;

class Adm_Test_dataBase_Admin extends Admin
{
}
class Adm_Test_fileBase_Admin extends Admin
{
}


class Adm_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $classes = [Adm::ADMIN];
        
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        Mod::get()->reset();
        $res= Adm::get()->reset();
        $this->assertTrue($res);
        
        $mod= Mod::get();
        $adm= Adm::get();
        
        $this->assertNotNull($adm);

        
        $prm['application']['base']='fileBase';
        $adm->init($prm['application'], $prm['fileBase']);
        $prm['application']['base']='dataBase';
        $adm->init($prm['application'], $prm['dataBase']);
        
        
        $this->assertEquals(2, count($mod->getMods()));

        $mod->begin();
        
        $res = Adm::get()->initMeta($prm['application'], $prm['dataBase']);
        $res = Adm::get()->initMeta($prm['application'], $prm['fileBase']);
        
        $mod->end();
        
        return $prm;
    }
    /**
    * @depends testInit
    *
    */
    /**
     * @depends testInit
     */
    public function testNew($prm)
    {
        $mod= Mod::get();
        $adm= Adm::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $obj = $adm->begin($prm['application'], $bd);
            
            $this->assertTrue($adm->isNew());
            $this->assertEquals(1, $obj->getId());
            
            $mod->end();
        }
        return $prm;
    }
    /**
     * @depends testNew
     */
    public function testExists($prm)
    {
        $mod= Mod::get();
        $adm= Adm::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $obj = Adm::get()->begin($prm['application'], $bd);
            
            $this->assertEquals(1, $obj->getId());
            $this->assertFalse(Adm::get()->isNew());
            
            $mod->end();
        }
        return $prm;
    }
}
