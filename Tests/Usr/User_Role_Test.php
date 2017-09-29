<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Mod\ModUtils;

class User_Role_Test_dataBase_User extends User
{
}
class User_Role_Test_fileBase_User extends User
{
}

class User_Role_Test_dataBase_Role extends Role
{
}
class User_Role_Test_fileBase_Role extends Role
{
}

class User_Role_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        $classes = ['User','Role'];
        
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
            $x->getErrLog()->show();
            $this->assertEquals(1, $res);
     
            $x = new Model($bd['Role']);
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $x = new Model($bd['Role']);
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
            
            $x = new Model($bd['Role'], 1);
            $res= $x->setVal('Name', 'test1');
            $x->save();
            $this->assertFalse($x->isErr());

            $x = new Model($bd['User'], 1);
            $res= $x->getValues('Role');
            $this->assertEquals([1,2], $res);
            
            $obj=$x->getCobj();
            $res = $obj->checkAttr('Role', 3);
            $this->assertFalse($res);

            $obj=$x->getCobj();
            $res = $obj->checkAttr('Role', 2);
            $this->assertTrue($res);
            
            $x->setVal('Role', 1);
            $x->save();
            $this->assertFalse($x->isErr());
            
            $mod->end();
        }
        return $prm;
    }

    /**
    * @depends  testset
    */

    public function testsetRole($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd['Role'], 1);
            $obj=$x->getCobj();
            
            $this->assertNull($obj->getSpec());
                
            $spec = [["true", "true", "true"]];
            $val = json_encode($spec);
            $x->setVal('JSpec', $val);
            $obj=$x->getCobj();
            $this->assertEquals($spec, $obj->getSpec());
            
            $mod->end();
        }
        return $prm;
    }
}
