<?php

use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Model;

class Admin_Test_dataBase_1 extends Admin
{
}
class Admin_Test_fileBase_1 extends Admin
{
}

class Admin_Test extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $name = 'atest';
        $classes = ['Admin'];
        $bsname = end(explode('\\', get_called_class()));
        $bases= UtilsC::initHandlers($name, $classes, $bsname);
        $res = UtilsC::initClasses($bases);
        $this->assertTrue($res);
        return $bases;
    }
    /**
    * @depends testInit
    */
    public function testsave($bases)
    {

        foreach ($bases as $base) {
            list($db,$bd) = $base;
        
            $db->beginTrans();
            
            $x = new Model($bd['Admin']);
            $res=$x->deleteMod();
            $this->assertTrue($res);
            
            $x = new Model($bd['Admin']);
            $x->setVal('Application', '../Tests/Adm');
            $x->setVal('Init', true);
            $res=$x->save();
            $this->assertequals(1, $res);
     
            $db->commit();
        }
        return $bases;
    }
 
    /**
    * @depends  testsave
    */
    
    public function testget($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Admin'], 1);
            $res= $x->getVal('Application');
            $this->assertEquals('../Tests/Adm', $res);
            $x->save();
            
            $this->assertFalse($x->isErr());
            
            $db->commit();
        }
        return $bases;
    }

    /**
    * @depends  testget
    */

    public function testget2($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            $db->beginTrans();
            
            $x = new Model($bd['Admin'], 1);
            $res= $x->getVal('Application');
            $this->assertEquals('../Tests/Adm', $res);
            
            $x = new Model($bd['Admin']);
            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $res=$x->delet();
            $this->assertFalse($res);
            
            $db->commit();
        }
        return $bases;
    }

    /**
    * @depends  testget2
    */
    public function testsave2($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;

            $db->beginTrans();
            
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
            $db->commit();
        }
        
        return $bases;
    }
}
