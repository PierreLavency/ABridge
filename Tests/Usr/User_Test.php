<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\CstError;

use ABridge\ABridge\Usr\User;

class User_Test_dataBase_User extends User
{
}
class User_Test_fileBase_User extends User
{
}

class User_Test extends PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $classes = ['User'];
        
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        Mod::get()->reset();
        
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
     
            $obj = $x->getCobj();
            $res = $obj->authenticate(null, null);
            $this->assertTrue($res);
     
            $mod->end();
        }
        return $prm;
    }
 
    /**
    * @depends  testsave
    */
    
    public function testget($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd['User'], 1);
            $res= $x->getVal('Password');
            $this->assertNull($res);
     
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password');
            $x->setVal('NewPassword1', 'Password');
            $x->setVal('NewPassword2', 'Password');
     
            $res=$x->save();
            $this->assertFalse($x->isErr());
            
            $mod->end();
        }
        return $prm;
    }

    /**
    * @depends  testget
    */

    public function testget2($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd['User'], 1);
            $res= $x->getVal('Password');
            $this->assertNull($res);

            $res= $x->getVal('UserId');
            $this->assertEquals('test', $res);

            $res= $x->getVal('MetaData');
            $this->assertNotNull($res);
            
            $x->setVal('UserId', 'test2');
            $x->setVal('Password', 'Password');

            $res=$x->save();
            $this->assertFalse($x->isErr());

            $obj = $x->getCobj();
            
            $res = $obj->authenticate('test2', 'Password');
            $this->assertTrue($res);
            
            $res = $obj->authenticate('test', 'Password');
            $this->assertFalse($res);

            $res = $obj->authenticate('test2', 'Password2');
            $this->assertFalse($res);
            
            $mod->end();
        }
        return $prm;
    }

    /**
    * @depends  testget2
    */
    public function testerr($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $this->assertNotNull($x = new Model($bd['User'], 1));
            $x->setVal('UserId', 'test3');
            $x->setVal('Password', 'Password2');
            $x->save();
            
            $this->assertEquals($x->getErrLine(), CstError::E_ERC057);
           
            $x->setVal('Password', 'Password');

            $x->setVal('NewPassword1', 'Password');
            $x->setVal('NewPassword2', 'Password2');
            
            $x->save();
            
            $this->assertEquals($x->getErrLine(), CstError::E_ERC058);
            
            $mod->end();
        }
        return $prm;
    }
}
