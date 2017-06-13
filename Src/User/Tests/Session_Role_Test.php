<?php

require_once 'UtilsC.php';
require_once("Model.php");
require_once("Handler.php");
require_once 'CModel.php';
require_once '/User/Src/Role.php';
require_once '/User/Src/Session.php';

class Session_Role_Test_dataBase_2 extends Role
{
};
class Session_Role_Test_fileBase_2 extends Role
{
};

class Session_Role_Test_dataBase_1 extends Session
{
};
class Session_Role_Test_fileBase_1 extends Session
{
};


class Session_Role_Test extends PHPUnit_Framework_TestCase
{
   
    public function testInit()
    {
        $name = 'test';
        $classes = ['Session','Role'];
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

            $x = new Model($bd['Role']);
            $x->setVal('Name', 'Default');

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
    
    public function testGet($bases)
    {

        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Session'], 1);

            $res= $x->getVal('Role');
            
            $this->assertEquals(1, $res);
            
            $db->commit();
        }
        
        return $bases;
    }
 
    /**
    * @depends  testUpdt
    */

    public function itestErr($bases)
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
            $this->assertEquals($x->getErrLine(), E_ERC059.":testtt");
            
            $x->setVal('UserId', 'test');
            $x->setVal('Password', 'Password2');

            $x->save();
            $this->assertEquals($x->getErrLine(), E_ERC057);
            $db->commit();
        }
        
        return $bases;
    }
}
