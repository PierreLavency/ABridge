<?php
require_once 'UtilsC.php';
    
require_once("Model.php");
require_once("Handler.php");
require_once 'CModel.php';
require_once '/User/Src/Session.php';


class Session_Test_dataBase_1 extends Session
{
}
class Session_Test_fileBase_1 extends Session
{
}


class Session_Test extends PHPUnit_Framework_TestCase
{


    public function testInit()
    {
        $name = 'test';
        $classes = ['Session'];
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
        $idl = [];
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
        
            $x = new Model($bd['Session']);

            $obj=$x->getCobj();
            $idl[] = $obj->getKey();
            
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $db->commit();
        }
        
        return [$bases,$idl];
    }

    /**
    * @depends  testsave
    */
    
    public function testKey($basesId)
    {
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Session']);
            $x = $x->getCobj();
            
            $res = $x->findValidSession($idl[$i]);

            list($obj,$pobj) = $res;
    
            $this->assertNotNull($obj);
            $this->assertNotNull($pobj);
        
            $cobj = $obj->getCobj();
            $cpobj = $pobj->getCobj();
        
            $this->assertEquals($idl[$i], $cobj->getKey());
            $this->assertEquals($idl[$i], $cpobj->getKey());
            
            $i++;
            $db->commit();
        }
        
        return $basesId;
    }
 
    /**
    * @depends  testKey
    */

    public function testdel($basesId)
    {
        
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
        
            $x = new Model($bd['Session']);
            $x = $x->getCobj();
            
            $res = $x->findValidSession($idl[$i]);

            list($obj,$pobj) = $res;
        
            $obj->delet();
            
            $this->assertEquals(0, $obj->getValN('ValidFlag'));
            
            $i++;
            $db->commit();
        }
        
        return $basesId;
    }
    
    /**
    * @depends  testdel
    */
    public function testKey2($basesId)
    {
        
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();

            $x = new Model($bd['Session']);
            $x = $x->getCobj();
            
            $res = $x->findValidSession($idl[$i]);

            list($obj,$pobj) = $res;
    
            $this->assertNull($obj);
            $this->assertNotNull($pobj);
        
            $cpobj = $pobj->getCobj();
        
            $this->assertEquals($idl[$i], $cpobj->getKey());
            
            $i++;
            $db->commit();
        }
        
        return $basesId;
    }

    /**
    * @depends  testKey2
    */

    public function testdel2($basesId)
    {
        
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
        
            $x = new Model($bd['Session']);
            $x = $x->getCobj();
            
            $res = $x->findValidSession($idl[$i]);

            list($obj,$pobj) = $res;
        
            $this->assertNotNull($pobj);
            
            $res = $pobj->delet();
            
            $this->assertTrue($res);
                    
            $i++;
            $db->commit();
        }
        
        return $basesId;
    }
    
    /**
    * @depends  testdel2
    */
    public function testKey3($basesId)
    {
        
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();

            $x = new Model($bd['Session']);
            $x = $x->getCobj();
            
            $res = $x->findValidSession($idl[$i]);

            list($obj,$pobj) = $res;
    
            $this->assertNull($obj);
            $this->assertNull($pobj);
        
            
            $i++;
            $db->commit();
        }
        
        return $basesId;
    }
}
