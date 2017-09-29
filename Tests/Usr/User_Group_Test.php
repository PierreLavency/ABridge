<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\UserGroup;
use ABridge\ABridge\Mod\ModUtils;

class User_Group_Test_dataBase_User extends User
{
}
class User_Group_Test_fileBase_User extends User
{
}

class User_Group_Test_dataBase_UserGroup extends UserGroup
{
}
class User_Group_Test_fileBase_UserGroup extends UserGroup
{
}

class User_Group_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        $classes = ['User','UserGroup'];
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        Mod::reset();
        
        $mod= Mod::get();
        
        $mod->init($prm['application'], $prm['handlers']);
        
        $mod->begin();
        
        $res = ModUtils::initModBindings($prm['dataBase']);
        $res = ($res && ModUtils::initModBindings($prm['fileBase']));
        
        $mod->end();
        
        $this->assertTrue($res);
        
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
            
            $x = new Model($bd['User']);
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $x = new Model($bd['UserGroup']);
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $x = new Model($bd['UserGroup']);
            $res=$x->save();
            $this->assertEquals(2, $res);
     
            $mod->end();
        }
        return $prm;
    }

    /**
    * @depends  testsave
    */
    public function testset($prm)
    {
        
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
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
            
            $mod->end();
        }
        return $prm;
    }

    /**
    * @depends  testset
    */

    public function testgetMeta($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd['User'], 1);
            $y = $x->getRef('UserGroup');
            $res=$y->getVal('MetaData');
            
            $this->assertNotNull($res);

            $res=$y->getVal('Name');
            
            $this->assertNotNull($res);
            
            $mod->end();
        }
        return $prm;
    }
}
