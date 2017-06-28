<?php
require_once 'UtilsC.php';
require_once 'Model.php';
require_once 'Handler.php';
require_once 'CModel.php';
require_once '/Adm/Src/Admin.php';

class Admin_Test_dataBase_1 extends Admin
{
}
class Admin_Test_fileBase_1 extends Admin
{
}

class Admin_Test extends PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $name = 'atest';
        $classes = ['Admin'];
        $bsname = get_called_class();
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
            $x->setVal('Application', '../Src/Adm/Tests');
            $x->setVal('Init', true);
            $res=$x->save();
            $this->assertequals(1,$res);
     
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
            $this->assertEquals('../Src/Adm/Tests',$res);
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
            $this->assertEquals('../Src/Adm/Tests',$res);
            
            $x = new Model($bd['Admin']);
            $res=$x->save();
            $this->assertEquals(1,$res);
            
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
            $this->assertEquals('../Src/Adm/Tests',$res);
            
            $x->setVal('Meta', true);
            $x->setVal('Load', true);
            $x->setVal('Delta',true);
            
            $res=$x->save();
            
            $this->assertEquals(1,$res);
            $x->getErrLog()->show();
            $this->assertFalse($x->isErr());
            $db->commit();
        }
        
        return $bases;
    }
}
