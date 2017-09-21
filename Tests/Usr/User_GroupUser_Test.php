<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\UserGroup;
use ABridge\ABridge\Usr\GroupUser;
use ABridge\ABridge\CstError;

class User_GroupUser_Test_dataBase_User extends User
{
}
class User_GroupUser_Test_fileBase_User extends User
{
}

class User_GroupUser_Test_dataBase_UserGroup extends UserGroup
{
}
class User_GroupUser_Test_fileBase_UserGroup extends UserGroup
{
}

class User_GroupUser_Test_dataBase_GroupUser extends GroupUser
{
}
class User_GroupUser_Test_fileBase_GroupUser extends GroupUser
{
}


class User_GroupUser_Test extends PHPUnit_Framework_TestCase
{

    
    public function testInit()
    {
        $classes = ['User','UserGroup','GroupUser'];
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        Mod::reset();
        
        $mod= Mod::get();
        
        $mod->init($prm['application'], $prm['handlers']);
        
        $mod->begin();
        
        $res = $mod->initModBindings($prm['dataBase']);
        $res = ($res && $mod->initModBindings($prm['fileBase']));
        
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
            
            $d = new Model($bd['GroupUser'], 1);
            $res= $d->delet();
            $this->assertFalse($res);
            $this->assertEquals(CstError::E_ERC052.':UserGroup', $d->getErrLine());
                        
            
            $mod->end();
        }
        return $prm;
    }
}
