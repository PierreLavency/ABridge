<?php
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\UtilsC;

class Usr_Test_dataBase_Session extends Session
{
}
class Usr_Test_fileBase_Session extends Session
{
}


class Usr_Test extends PHPUnit_Framework_TestCase
{


    public function testInit()
    {
        $classes = [Usr::SESSION];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['dataBase']);

        Mod::reset();
        Usr::reset();
        
        $mod= Mod::get();
        $usr= Usr::get();
        
        try {
            $r='';
            $usr->begin();
        } catch (Exception $e) {
            $r= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC067.':Usr', $r);
        
        try {
            $r='';
            $usr->initMeta();
        } catch (Exception $e) {
            $r= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC067.':Usr', $r);
        
        
/*
        $prm['application']['base']='fileBase';
        $usr->init($prm['application'], $prm['fileBase']);
        */
        $prm['application']['base']='dataBase';
        $usr->init($prm['application'], $prm['dataBase']);
        
        try {
            $r='';
            $usr->init($prm['application'], $prm['dataBase']);
        } catch (Exception $e) {
            $r= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC068.':Usr', $r);
        
        $mod->begin();
        
        $res = $usr->initMeta();
//        $res = $res and usr::initMeta($prm['application'], $prm['fileBase']);
        
        $mod->end();

        
        $this->assertEquals(1, count($res));
        
        return $prm;
    }
    /**
    * @depends testInit
    */
    public function testNew($prm)
    {
        $mod= Mod::get();
        $usr= Usr::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $cobj = $usr->begin($prm['application'], $bd);
 
            $session = $cobj->getMod();

            $this->assertTrue($usr->isNew());
            $this->assertEquals(1, $session->getId());
     
            $mod->end();
        }
        return $prm;
    }

     /**
    * @depends testNew
    */
    public function testExists($prm)
    {
        $mod= Mod::get();
        $usr= Usr::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x =  new Model($bd['Session'], 1);
            $name = $prm['application']['name'].$bd['Session'];
            $_COOKIE[$name]= $x->getVal('BKey');
            
            $cobj = Usr::get()->begin($prm['application'], $bd);

            $this->assertFalse(Usr::get()->isNew());
     
            $mod->end();
        }
        return $prm;
    }
    
    /**
    * @depends testExists
    */
    public function testExistsNew($prm)
    {
        $mod= Mod::get();
        $usr= Usr::get();
 
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x =  new Model($bd['Session'], 1);
            $name = $prm['application']['name'].$bd['Session'];
            $_COOKIE[$name]= $x->getVal('BKey');
            $x->delet();
                        
            $cobj = Usr::get()->begin($prm['application'], $bd);
            $session = $cobj->getMod();

            $this->assertTrue($cobj->isNew());
            $this->assertEquals(2, $session->getId());
            
            $mod->end();
        }
        return $prm;
    }
    
    /**
     * @depends testExistsNew
     */
    public function testCleanUp($prm)
    {
        $mod= Mod::get();
        $usr= Usr::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();

            $x =  new Model($bd['Session'], 2);
            $name = $prm['application']['name'].$bd['Session'];
            $_COOKIE[$name]= $x->getVal('BKey');
            
            Usr::$cleanUp=true;
                        
            $cobj = Usr::get()->begin($prm['application'], $bd);
            $session = $cobj->getMod();
            
            $this->assertTrue($cobj->isNew());
            $this->assertEquals(3, $session->getId());
            
            Usr::$cleanUp=false;
            
            $mod->end();
        }
        return $prm;
    }
    /**
     * @depends testCleanUp
     */
    public function testNoBindings($prm)
    {
        $prm=UtilsC::genPrm([Usr::SESSION], get_called_class());
        $prm['application']['base']='memBase';
        
        Usr::reset();
        Usr::get()->init($prm['application'], [Usr::SESSION]);

        
        $this->assertEquals('ABridge\ABridge\Usr\Session', Mod::get()->getClassMod(Usr::SESSION));
        
        Mod::get()->begin();
        
        $res = Usr::get()->initMeta();
        //        $res = $res and usr::initMeta($prm['application'], $prm['fileBase']);
        
        Mod::get()->end();
        
        $hdl= Usr::get()->begin();
        
        $this->assertNotNull($hdl);
    }
}
