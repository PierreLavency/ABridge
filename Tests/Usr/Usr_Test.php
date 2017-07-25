<?php
use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;


use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Usr\Usr;

class Usr_Test_dataBase_1 extends Session
{
}
class Usr_Test_fileBase_1 extends Session
{
}


class Usr_Test extends PHPUnit_Framework_TestCase
{


    public function testInit()
    {
        $name = 'test';
        $classes = ['Session'];
        $bsname = get_called_class();
        $bases= UtilsC::initHandlers($name, $classes, $bsname);
        $res = UtilsC::initClasses($bases);
        $this->assertTrue($res);
        
        return $bases;
    }
    /**
    * @depends testInit
    */
    public function testNew($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
        
            $cobj = Usr::begin($bd['Session'], [$bd['Session']]);
            $session = $cobj->getMod();

            $this->assertTrue($cobj->isNew());
//            $this->assertTrue(Usr::isNew());
            $this->assertEquals(1, $session->getId());
     
            $db->commit();
        }
        return $bases;
    }

     /**
    * @depends testNew
    */
    public function testExists($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            $x =  new Model($bd['Session'], 1);
            $_COOKIE[$bd['Session']]= $x->getVal('BKey');
            
            $cobj = Usr::begin($bd['Session'], [$bd['Session']]);
            $session = $cobj->getMod();

            $this->assertFalse($cobj->isNew());
//            $this->assertFalse(Usr::isNew());
     
            $db->commit();
        }
        return $bases;
    }
    
    /**
    * @depends testExists
    */
    public function testExistsNew($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            $x =  new Model($bd['Session'], 1);
            $_COOKIE[$bd['Session']]= $x->getVal('BKey');
            $x->delet();
            
             
            $cobj = Usr::begin($bd['Session'], [$bd['Session']]);
            $session = $cobj->getMod();

 //           $this->assertTrue(Usr::isNew());
            $this->assertTrue($cobj->isNew());
            $this->assertEquals(2, $session->getId());
            
            $db->commit();
        }
        return $bases;
    }
    
    /**
     * @depends testExistsNew
     */
    public function testCleanUp($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            $x =  new Model($bd['Session'], 2);
            $_COOKIE[$bd['Session']]= $x->getVal('BKey');
            
            Usr::$cleanUp=true;
            
            
            $cobj = Usr::begin($bd['Session'], [$bd['Session']]);
            $session = $cobj->getMod();
            
 //           $this->assertTrue(Usr::isNew());
            $this->assertTrue($cobj->isNew());
            $this->assertEquals(3, $session->getId());
            
            Usr::$cleanUp=false;
            $db->commit();
        }
        return $bases;
    }
}
