<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;


use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\UserGroup;

class User_Group_Test_dataBase_1 extends User
{
}
class User_Group_Test_fileBase_1 extends User
{
}

class User_Group_Test_dataBase_2 extends UserGroup
{
}
class User_Group_Test_fileBase_2 extends UserGroup
{
}

class User_Group_Test extends PHPUnit_Framework_TestCase
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
        $classes = ['User','UserGroup'];
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
     
            $x = new Model($bd['UserGroup']);
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $x = new Model($bd['UserGroup']);
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
            
            $x = new Model($bd['UserGroup'], 1);
            $res= $x->setVal('Name', 'test1');
            $x->save();
            $this->assertFalse($x->isErr());

            $x = new Model($bd['User'], 1);
            $res= $x->getValues('UserGroup');
            $this->assertEquals([1,2], $res);
            
            $obj=$x->getCobj();
            $res = $obj->checkAttr('UserGroup', 3);
            $this->assertFalse($res);

            $res = $obj->checkAttr('UserGroup', 2);
            $this->assertTrue($res);
            
            $x->setVal('UserGroup', 1);
            $x->save();
            $this->assertFalse($x->isErr());
            
            $db->commit();
        }
        return $bases;
    }

    /**
    * @depends  testset
    */

    public function testgetMeta($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;

            $db->beginTrans();
            
            $x = new Model($bd['User'], 1);
            $y = $x->getRef('UserGroup');
            $res=$y->getVal('MetaData');
            
            $this->assertNotNull($res);

            $res=$y->getVal('Name');
            
            $this->assertNotNull($res);
            
            $db->commit();
        }
        return $bases;
    }
}
