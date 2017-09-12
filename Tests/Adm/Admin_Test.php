<?php

use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;

class Admin_Test_dataBase_Admin extends Admin
{
}
class Admin_Test_fileBase_Admin extends Admin
{
}

class Admin_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $classes = [Adm::ADMIN];
        $prm=UtilsC::genPrm($classes, get_called_class());

        Mod::get()->reset();

        $mod= Mod::get();
        
        $mod->init($prm['application'], $prm['handlers']);

        $mod->begin();
        
        $res = Adm::get()->initMeta($prm['application'], $prm['dataBase']);
        $res = Adm::get()->initMeta($prm['application'], $prm['fileBase']);
        
        $mod->end();
       
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
            $x->setVal('name', '../Tests/Adm');
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
            

            $x = new Model($bd[Adm::ADMIN], 1);
            $x->setVal('Delta', true);
            $x->save();
            
            $this->assertFalse($x->isErr());
            
            $res= $x->getVal('name');
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
            $res= $x->getVal('name');
            $this->assertEquals('../Tests/Adm', $res);
            
            $x->setVal('Meta', true);
            $res = Adm::get()->InitMeta([], $bd); //to simulate 
            
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
    		$res= $x->getVal('name');
    		$this->assertEquals('../Tests/Adm', $res);
    		$this->assertNotNull($x->getVal('MetaData'));
    		
    		$x->setVal('Load', true);
    		
    		$res=$x->save();
    		$this->assertEquals(1, $res);
    		
    		$res=$x->delet();
    		$this->assertFalse($res);
    		
    		$mod->end();
    	}
    	return $prm;
    }
}
