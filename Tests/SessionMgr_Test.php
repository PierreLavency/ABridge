<?php
require_once 'UtilsC.php';
    
require_once 'Model.php';
require_once 'Handler.php';
require_once 'CModel.php';

require_once '/User/Src/Session.php';


class SessionMgr_Test_dataBase_1 extends Session
{
}
class SessionMgr_Test_fileBase_1 extends Session
{
}


class SessionMgr_Test extends PHPUnit_Framework_TestCase
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
        
            $x = new SessionMgr($bd['Session'], $bd['Session']);
            $cobj = $x->getSession();
            $session = $cobj->getMod();

            $this->assertTrue($cobj->isNew());
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
            
            $x = new SessionMgr($bd['Session'], $bd['Session']);
            $cobj = $x->getSession();
            $session = $cobj->getMod();

            $this->assertFalse($cobj->isNew());
     
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
            
             
            $x = new SessionMgr($bd['Session'], $bd['Session']);
            $cobj = $x->getSession();
            $session = $cobj->getMod();

            $this->assertTrue($cobj->isNew());
            $this->assertEquals(2, $session->getId());
            
            $db->commit();
        }
        return $bases;
    }
}
