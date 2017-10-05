<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class Adm_Test_dataBase_Admin extends Admin
{
}
class Adm_Test_fileBase_Admin extends Admin
{
}
class Adm_Test_memBase_Admin extends Admin
{
}

class Adm_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $classes = [Adm::ADMIN];
        $baseTypes=[
                'dataBase',
/*        		'fileBase',
        		'memBase',
  */
        ];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        Mod::reset();
        $res= Adm::reset();
        $this->assertTrue($res);
        
        $mod= Mod::get();
        $adm= Adm::get();
        
        $this->assertNotNull($adm);

        
        try {
            $r='';
            $adm->begin();
        } catch (Exception $e) {
            $r= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC067.':Adm', $r);

        try {
            $r='';
            $adm->initMeta();
        } catch (Exception $e) {
            $r= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC067.':Adm', $r);
        
        $prm['application']['base']='dataBase';
        $adm->init($prm['application'], $prm['dataBase']);
        $this->assertEquals(1, count($mod->getMods()));
        
        try {
            $r='';
            $adm->init($prm['application'], $prm['dataBase']);
        } catch (Exception $e) {
            $r= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC068.':Adm', $r);
        

        $mod->begin();
        
        $res = Adm::get()->initMeta();
        
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
