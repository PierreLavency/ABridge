<?php

use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\CstError;

use ABridge\ABridge\Usr\User;

use ABridge\ABridge\Usr\Session;

class Session_User_Test_dataBase_User extends User
{
};
class Session_User_Test_fileBase_User extends User
{
};


class Session_User_Test_dataBase_Session extends Session
{
};
class Session_User_Test_fileBase_Session extends Session
{
};


class Session_User_Test extends PHPUnit_Framework_TestCase
{

    
    public function testInit()
    {
        $classes = ['Session','User'];
        
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        Mod::get()->reset();
        
        $mod= Mod::get();
        
        $mod->init($prm['application'], $prm['handlers']);
        
        $mod->begin();
        
        $res = UtilsC::createMods($prm['dataBase']);
        $res = $res and UtilsC::createMods($prm['fileBase']);
        
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
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password');
            $x->setVal('NewPassword1', 'Password');
            $x->setVal('NewPassword2', 'Password');

            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $x = new Model($bd['Session']);
            
            $res=$x->save();
            $this->assertEquals(1, $res);
 

            $mod->end();
        }
        
        return $prm;
    }

    /**
    * @depends  testsave
    */
    
    public function testUpdt($prm)
    {

        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd['Session'], 1);
            
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password');
            
            $x->save();
            
            $this->assertFalse($x->isErr());
            
            $mod->end();
        }
        
        return $prm;
    }
 
    /**
    * @depends  testUpdt
    */

    public function testErr($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
        
            $x = new Model($bd['Session'], 1);
            
            $res= $x->getVal('Password');
            $this->assertNull($res);
            
            $res= $x->getVal('UserId');
            $this->assertEquals('test', $res);
            
            $x->setVal('UserId', 'testtt');
            
            $x->save();
            $this->assertEquals($x->getErrLine(), CstError::E_ERC059.":".$bd['User'].":testtt");
            
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password2');

            $x->save();
            $this->assertEquals($x->getErrLine(), CstError::E_ERC057);
            $mod->end();
        }
        
        return $prm;
    }
}
