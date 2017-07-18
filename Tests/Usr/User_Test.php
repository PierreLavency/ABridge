<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;

use ABridge\ABridge\CstError;

use ABridge\ABridge\Usr\User;

class User_Test_dataBase_1 extends User
{
}
class User_Test_fileBase_1 extends User
{
}

class User_Test extends PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $name = 'test';
        $classes = ['User'];
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
     
            $obj = $x->getCobj();
            $res = $obj->authenticate(null, null);
            $this->assertTrue($res);
     
            $db->commit();
        }
        return $bases;
    }
 
    /**
    * @depends  testsave
    */
    
    public function testget($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['User'], 1);
            $res= $x->getVal('Password');
            $this->assertNull($res);
     
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password');
            $x->setVal('NewPassword1', 'Password');
            $x->setVal('NewPassword2', 'Password');
     
            $res=$x->save();
            $this->assertFalse($x->isErr());
            
            $db->commit();
        }
        return $bases;
    }

    /**
    * @depends  testget
    */

    public function testget2($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            $db->beginTrans();
            
            $x = new Model($bd['User'], 1);
            $res= $x->getVal('Password');
            $this->assertNull($res);

            $res= $x->getVal('UserId');
            $this->assertEquals('test', $res);

            
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
            
            $db->commit();
        }
        return $bases;
    }

    /**
    * @depends  testget2
    */
    public function testerr($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;

            $db->beginTrans();
            
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
            
            $db->commit();
        }
        
        return $bases;
    }
}
