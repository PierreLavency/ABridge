<?php
require_once 'UtilsC.php';
require_once("Model.php");
require_once("Handler.php");
require_once 'CModel.php';
require_once 'User.php';
require_once 'Role.php';
require_once 'Distribution.php';

class User_Distribution_Test_dataBase_1 extends User
{
}
class User_Distribution_Test_fileBase_1 extends User
{
}

class User_Distribution_Test_dataBase_2 extends Role
{
}
class User_Distribution_Test_fileBase_2 extends Role
{
}

class User_Distribution_Test_dataBase_3 extends Distribution
{
}
class User_Distribution_Test_fileBase_3 extends Distribution
{
}


class User_Distribution_Test extends PHPUnit_Framework_TestCase
{

    
    public function testInit()
    {
        $name = 'test';
        $classes = ['User','Role','Distribution'];
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

            $x = new Model($bd['Distribution']);
            $x->setVal('ofRole', 1);
            $x->setVal('toUser', 1);
            $res=$x->save();
            $this->assertEquals(1, $res);

            $obj = $x->getCobj();
            $res= $obj->initMod([]);
            $this->assertFalse($res);
            
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
            
            $x = new Model($bd['User'], 1);
            $res= $x->getValues('DefaultRole');
            $this->assertEquals([1], $res);
            
            $obj=$x->getCobj();
            $res = $obj->checkRole(2);
            $this->assertFalse($res);

            $res = $obj->checkRole(1);
            $this->assertTrue($res);
            
            $x->setVal('DefaultRole', 1);
            $x->save();
            $this->assertFalse($x->isErr());

            $res= $x->setVal('DefaultRole', 2);
            $this->assertFalse($res);
            
            $db->commit();
        }
        return $bases;
    }
}
