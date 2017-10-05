<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\UtilsC;

class Admin_Test_dataBase_Admin extends Admin
{
}
class Admin_Test_fileBase_Admin extends Admin
{
}
class Admin_Test_memBase_Admin extends Admin
{
}
class Admin_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $classes = [Adm::ADMIN];
        $baseTypes=['memBase'];
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);

        Mod::reset();
        Log::reset();

        $mod= Mod::get();
        
        $mod->init($prm['application'], $prm['handlers']);
       
        return $prm;
    }
    /**
    * @depends testInit
    */
    public function testsave($prm)
    {

        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
          
            $x = new Model($bd[Adm::ADMIN]);
            $x->setVal('Name', '../Tests/Adm');
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $mod->end();
        }
        return $prm;
    }
 
    /**
    * @depends  testsave
    */
    
    public function testdelta($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            Log::get()->begin();

            $x = new Model($bd[Adm::ADMIN], 1);
            $x->setVal('Delta', true);

            $x->save();
            
            $this->assertFalse($x->isErr());
            
            $res= $x->getVal('Name');
            $this->assertEquals('../Tests/Adm', $res);
                       
            $mod->end();
        }
        return $prm;
    }


    /**
     * @depends  testdelta
     */
    
    public function testMeta($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd[Adm::ADMIN], 1);
            $res= $x->getVal('Name');
            $this->assertEquals('../Tests/Adm', $res);
            
            $x->setVal('Meta', true);
            
            $y = new Model($bd[Adm::ADMIN]);
            $res = $y->deleteMod(); //to simulate
            
            $res=$x->save();
            $this->assertEquals(1, $res);

            
            $mod->end();
        }
        return $prm;
    }
    
    /**
     * @depends  testMeta
     */
    
    public function testLoad($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd[Adm::ADMIN], 1);
            $res= $x->getVal('Name');
            $this->assertEquals('../Tests/Adm', $res);
            $this->assertNotNull($x->getVal('MetaData'));
            
            $x->setVal('Load', true);
            
            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $res = $x->getVal('ModState');
            $this->assertNotNull($res);
            
            $res = $x->getVal('Model');
            $this->assertNotNull($res);
            
            $res = $x->getVal('StateHandler');
            $this->assertNotNull($res);
            
            $res = $x->setVal('Model', 'notexists');

            $res = $x->getVal('Model');
            $this->assertEquals('notexists', $res);
            
            $res = $x->getVal('StateHandler');
            $this->assertEquals('', $res);
            
            $res=$x->delet();
            $this->assertFalse($res);
            
            $mod->end();
        }
        return $prm;
    }
}
