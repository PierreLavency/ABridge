<?php


require_once 'UtilsC.php';
require_once 'Model.php';
require_once 'Handler.php';
require_once 'CModel.php';
require_once '/User/Src/User.php';
require_once '/User/Src/Role.php';
require_once '/User/Src/Distribution.php';
require_once '/User/Src/Session.php';

class Session_All_Test_dataBase_2 extends User
{
}
class Session_All_Test_fileBase_2 extends User
{
}

class Session_All_Test_dataBase_3 extends Role
{
}
class Session_All_Test_fileBase_3 extends Role
{
}

class Session_All_Test_dataBase_1 extends Session
{
}
class Session_All_Test_fileBase_1 extends Session
{
}

class Session_All_Test_dataBase_4 extends Distribution
{
}
class Session_All_Test_fileBase_4 extends Distribution
{
}


class Session_All_Test extends PHPUnit_Framework_TestCase
{
   
    public function testInit()
    {
        $name = 'test';
        $classes = ['Session','User','Role','Distribution'];
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
        $idl = [];
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Role']);
            $x->setVal('Name', 'Default');
            $res=$x->save();
            $this->assertEquals(1, $res);

            $x = new Model($bd['Role']);
            $x->setVal('Name', 'test');
            $res=$x->save();
            $this->assertEquals(2, $res);
            
            $x = new Model($bd['User']);
            $x->setVal('UserId', 'test');
            $res=$x->save();
            $this->assertEquals(1, $res);

            $x = new Model($bd['Distribution']);
            $x->setVal('ofRole', 1);
            $x->setVal('toUser', 1);
            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $x = new Model($bd['Session']);
            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $db->commit();
        }
        
        return [$bases,$idl];
    }

    /**
    * @depends  testsave
    */
    
    public function testsave2($basesId)
    {
        list($bases,$idl) = $basesId;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Session'], 1);
			$sessionHdl = $x->getCobj();
			
            $res = $x->getVal('Role');
            $this->assertEquals(1, $res);
			
            $x->save();
            $this->assertFalse($x->isErr());
            $db->commit();
        }
        
        return $basesId;
    }
    /**
    * @depends  testsave2
    */
    
    public function testsave3($basesId)
    {
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Session'], 1);
            $x->setVal('UserId', 'test');
            $x->setVal('Role', null);

            $x->save();
            $this->assertEquals(E_ERC060.":", $x->getErrLine());
            
            $x->setVal('Role', 2);
            $x->save();
            $this->assertEquals(E_ERC060.":2", $x->getErrLine());
            $db->commit();
        }
        
        return $basesId;
    }
 
    /**
    * @depends  testsave3
    */
    
    public function testsave4($basesId)
    {
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x=new Model($bd['User'], 1);
            $x->setVal('DefaultRole', 1);
            $x->save();
            
            $x = new Model($bd['Session'], 1);
            $x->setVal('UserId', 'test');
            $x->setVal('Role', null);

            $x->save();
            $this->assertFalse($x->isErr());
            $db->commit();
        }
        
        return $basesId;
    }
    
    /**
    * @depends  testsave4
    */
    
    public function testsave5($basesId)
    {
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
                        
            $x = new Model($bd['Session'], 1);
            $x->setVal('UserId', 'test');
            $x->setVal('Role', null);

            $x->save();
            $this->assertFalse($x->isErr());
            
            $y = new Model($bd['Session']);
            $obj = $y->getCobj();
            $obj->initPrev($x);
            
            $y->save();
            $this->assertFalse($y->isErr());
            
            $this->assertEquals($x->getVal('Role'), $y->getVal('Role'));
            
            $db->commit();
        }
        
        return $basesId;
    }

    /**
    * @depends  testsave5
    */
    
    public function testsave6($basesId)
    {
        list($bases,$idl) = $basesId;
        $i=0;
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();

            $x = new Model($bd['Session']);
            $x->setVal('Role', 2);
            
            $x->save();
            $this->assertEquals(E_ERC060.":2", $x->getErrLine());
            
            $db->commit();
        }
        return $basesId;
    }
}
