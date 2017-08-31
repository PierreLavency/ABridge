<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;


use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\UserGroup;
use ABridge\ABridge\Usr\GroupUser;

class User_GroupUser_Test_dataBase_1 extends User
{
}
class User_GroupUser_Test_fileBase_1 extends User
{
}

class User_GroupUser_Test_dataBase_2 extends UserGroup
{
}
class User_GroupUser_Test_fileBase_2 extends UserGroup
{
}

class User_GroupUser_Test_dataBase_3 extends GroupUser
{
}
class User_GroupUser_Test_fileBase_3 extends GroupUser
{
}


class User_GroupUser_Test extends PHPUnit_Framework_TestCase
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
        $classes = ['User','UserGroup','GroupUser'];
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

            $x = new Model($bd['GroupUser']);
            $x->setVal('UserGroup', 1);
            $x->setVal('User', 1);
            $res=$x->save();
            $this->assertEquals(1, $res);

            $res=$x->getVal('MetaData');
            $this->assertNotNull($res);
            
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
            $res= $x->getValues('UserGroup');
            $this->assertEquals([1], $res);
            
            $obj=$x->getCobj();
            $res = $obj->checkAttr('UserGroup', 2);
            $this->assertFalse($res);

            $res = $obj->checkAttr('UserGroup', 1);
            $this->assertTrue($res);
            
            $x->setVal('UserGroup', 1);
            $x->save();
            $this->assertFalse($x->isErr());

            
            $res= $x->setVal('UserGroup', 2);
            $this->assertFalse($res);
            
            

                        
            
            $db->commit();
        }
        return $bases;
    }
}
