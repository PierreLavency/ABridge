<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;


use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Distribution;

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
        $prm=[
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];
        $name = 'test';
        $classes = ['User','Role','Distribution'];
        $bsname = get_called_class();
        $bases= UtilsC::initHandlers($name, $classes, $bsname, $prm);
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
            $x->setVal('Role', 1);
            $x->setVal('User', 1);
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
            $res= $x->getValues('Role');
            $this->assertEquals([1], $res);
            
            $obj=$x->getCobj();
            $res = $obj->checkAttr('Role', 2);
            $this->assertFalse($res);

            $res = $obj->checkAttr('Role', 1);
            $this->assertTrue($res);
            
            $x->setVal('Role', 1);
            $x->save();
            $this->assertFalse($x->isErr());

            
            $res= $x->setVal('Role', 2);
            $this->assertFalse($res);
            
            $db->commit();
        }
        return $bases;
    }
}
