<?php

use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Model;
use ABridge\ABridge\CstError;

use ABridge\ABridge\Usr\User;

use ABridge\ABridge\Usr\Session;

class Session_User_Test_dataBase_2 extends User
{
};
class Session_User_Test_fileBase_2 extends User
{
};


class Session_User_Test_dataBase_1 extends Session
{
};
class Session_User_Test_fileBase_1 extends Session
{
};


class Session_User_Test extends PHPUnit_Framework_TestCase
{

    
    public function testInit()
    {
        $name = 'test';
        $classes = ['Session','User'];
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
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password');
            $x->setVal('NewPassword1', 'Password');
            $x->setVal('NewPassword2', 'Password');

            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $x = new Model($bd['Session']);
            
            $res=$x->save();
            $this->assertEquals(1, $res);
 

            $db->commit();
        }
        
        return $bases;
    }

    /**
    * @depends  testsave
    */
    
    public function testUpdt($bases)
    {

        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Session'], 1);
            
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password');
            
            $x->save();
            
            $this->assertFalse($x->isErr());
            
            $db->commit();
        }
        
        return $bases;
    }
 
    /**
    * @depends  testUpdt
    */

    public function testErr($bases)
    {
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
        
            $x = new Model($bd['Session'], 1);
            
            $res= $x->getVal('Password');
            $this->assertNull($res);
            
            $res= $x->getVal('UserId');
            $this->assertEquals('test', $res);
            
            $x->setVal('UserId', 'testtt');
            
            $x->save();
            $this->assertEquals($x->getErrLine(), CstError::E_ERC059.":testtt");
            
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password2');

            $x->save();
            $this->assertEquals($x->getErrLine(), CstError::E_ERC057);
            $db->commit();
        }
        
        return $bases;
    }
}
