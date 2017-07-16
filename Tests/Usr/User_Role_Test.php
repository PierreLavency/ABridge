<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Model;


use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;

class User_Role_Test_dataBase_1 extends User
{
}
class User_Role_Test_fileBase_1 extends User
{
}

class User_Role_Test_dataBase_2 extends Role
{
}
class User_Role_Test_fileBase_2 extends Role
{
}

class User_Role_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        $name = 'test';
        $classes = ['User','Role'];
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
            
            $x = new Model($bd['User']);
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $x = new Model($bd['Role']);
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $x = new Model($bd['Role']);
            $res=$x->save();
            $this->assertEquals(2, $res);
     
            $db->commit();
        }
        return $bases;
    }

    /**
    * @depends  testsave
    */
    public function testset($bases)
    {
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Role'], 1);
            $res= $x->setVal('Name', 'test1');
            $x->save();
            $this->assertFalse($x->isErr());

            $x = new Model($bd['User'], 1);
            $res= $x->getValues('DefaultRole');
            $this->assertEquals([1,2], $res);
            
            $obj=$x->getCobj();
            $res = $obj->checkRole(3);
            $this->assertFalse($res);

            $obj=$x->getCobj();
            $res = $obj->checkRole(2);
            $this->assertTrue($res);
            
            $x->setVal('DefaultRole', 1);
            $x->save();
            $this->assertFalse($x->isErr());
            
            $db->commit();
        }
        return $bases;
    }

    /**
    * @depends  testset
    */

    public function testsetRole($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;

            $db->beginTrans();
            
            $x = new Model($bd['Role'], 1);
            $obj=$x->getCobj();
            
            $this->assertNull($obj->getSpec());
                
            $spec = [["true", "true", "true"]];
            $val = json_encode($spec);
            $x->setVal('JSpec', $val);
            $obj=$x->getCobj();
            $this->assertEquals($spec, $obj->getSpec());
            
            $db->commit();
        }
        return $bases;
    }
}
