<?php

use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\UtilsC;

class Hdl_Test_dataBase_Session extends Session
{
}
class Hdl_Test_fileBase_Session extends Session
{
}

class Hdl_Test extends \PHPUnit_Framework_TestCase
{

    
    public function testInit()
    {
        $classes = [Usr::SESSION];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['dataBase']);
        
        Mod::reset();
        Hdl::reset();
        Usr::reset();
        
        $mod= Mod::get();
        $hdl= Hdl::get();
        
        $hdl->init($prm['application'], ['Usr'=> $prm['dataBase']]);
        
        
        $mod->begin();
        $res = Usr::get()->initMeta();
        $mod->end();
        
        $this->assertEquals([], $hdl->initMeta());
        
        $this->assertEquals(1, count($res));
        
        return $prm;
    }
    /**
     * @depends testInit
     */
    public function testNew($prm)
    {

        $mod= Mod::get();
        $hdl= Hdl::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $handle = $hdl->begin();
                
            $this->assertTrue($hdl->isNew());
            $this->assertEquals(1, $handle->getId());
            $this->assertEquals('/Session/~', $handle->getRPath());
            $name = $prm['application']['name'].$bd['Session'];
            $prm['key']=$_COOKIE[$name];
            
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
        $hdl= Hdl::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $name = $prm['application']['name'].$bd['Session'];
            $_COOKIE[$name]= $prm['key'];
            $_SERVER['REQUEST_METHOD']='POST';
            $_SERVER['PATH_INFO']='/Session/~';
            $_GET['Action']=CstMode::V_S_UPDT;
            
            $handle = $hdl->begin();
            
            $mod->end();
            $this->assertFalse($hdl->isNew());
            $this->assertEquals('/Session/~', $handle->getRPath());
            $this->assertFalse($handle->isErr());
        }
        return $prm;
    }
    
    public function testInit2()
    {
        
        Mod::reset();
        Hdl::reset();
        
        $mod= Mod::get();
        $hdl= Hdl::get();
        
        $hdl->init([], []);

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']='/';
        $_GET['Action']=CstMode::V_S_READ;
        
        $mod->begin();

        $handle =$hdl->begin();
        
        $mod->end();
        
        $this->assertTrue($handle->nullobj());
    }
}
