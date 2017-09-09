<?php

use ABridge\ABridge\Adm\Admin;
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
    	$classes = ['Admin'];
    	$prm=UtilsC::genPrm($classes, get_called_class());

    	Mod::get()->reset();   	

    	$mod= Mod::get();
    	
    	$mod->init($prm['application'],$prm['handlers']);    	

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
            
            $x = new Model($bd['Admin']);
            $res=$x->deleteMod();
            $this->assertTrue($res);
            
            $x = new Model($bd['Admin']);
            $x->setVal('Application', '../Tests/Adm');
            $x->setVal('Init', true);
            $res=$x->save();
            $x->getErrLog()->show();
            $this->assertequals(1, $res);
     
            $mod->end();
    	}
    	return $prm;
    }
 
    /**
    * @depends  testsave
    */
    
    public function testget($prm)
    {
    	$mod= Mod::get();
    	
    	foreach ($prm['bindL'] as $bd) {
    		
    		$mod->begin();
            
            $x = new Model($bd['Admin'], 1);
            $res= $x->getVal('Application');
            $this->assertEquals('../Tests/Adm', $res);
            $x->save();
            
            $this->assertFalse($x->isErr());
            
            $mod->end();
    	}
    	return $prm;
    }

    /**
    * @depends  testget
    */

    public function testget2($prm)
    {
    	$mod= Mod::get();
    	
    	foreach ($prm['bindL'] as $bd) {
    		
    		$mod->begin();
            
            $x = new Model($bd['Admin'], 1);
            $res= $x->getVal('Application');
            $this->assertEquals('../Tests/Adm', $res);
            
            $x = new Model($bd['Admin']);
            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $res=$x->delet();
            $this->assertFalse($res);
            
            $mod->end();
    	}
    	return $prm;
    }

    /**
    * @depends  testget2
    */
    public function testsave2($prm)
    {
    	$mod= Mod::get();
    	
    	foreach ($prm['bindL'] as $bd) {
    		
    		$mod->begin();
    		
            $x = new Model($bd['Admin'], 1);
            $res= $x->getVal('Application');
            $this->assertEquals('../Tests/Adm', $res);
            
            $x->setVal('Meta', true);
            $x->setVal('Load', true);
            $x->setVal('Delta', true);
            
            $res=$x->save();
            
            $this->assertEquals(1, $res);
            $x->getErrLog()->show();
            $this->assertFalse($x->isErr());
            
            $mod->end();
    	}
    	return $prm;
    }
}
