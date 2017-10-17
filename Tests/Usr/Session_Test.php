<?php
use ABridge\ABridge\Mod\Find;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\UtilsC;

class Session_Test_dataBase_Session extends Session
{
}
class Session_Test_fileBase_Session extends Session
{
}


class Session_Test extends PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $classes = ['Session'];
        
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        Mod::reset();
        
        $mod= Mod::get();
        
        $mod->init($prm['application'], $prm['handlers']);
        
        $mod->begin();
        
        $res = ModUtils::initModBindings($prm['dataBase']);
        $res = ($res && ModUtils::initModBindings($prm['fileBase']));
        
        $mod->end();
        
        $this->assertTrue($res);
        
        return $prm;
    }
    /**
    * @depends testInit
    */
    public function testsave($prm)
    {
        $idl = [];
        $modS= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $modS->begin();
            
            $obj=$bd['Session']::getSession(0, [], 6000);
            $x = $obj->getMod();

            $idl[] = $obj->getKey();
            
            $res=$x->save();
            $this->assertEquals(1, $res);
     
            $modS->end();
        }
        
        return [$prm,$idl];
    }

    /**
    * @depends  testsave
    */
    
    public function testKey($basesId)
    {
        list($prm,$idl) = $basesId;
        $i=0;
        
        $modS= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $modS->begin();
            
            $obj = $bd['Session']::getSession($idl[$i], [], 6000);

        
            $this->assertEquals($idl[$i], $obj->getKey());
            
            $i++;
            $modS->end();
        }
        
        return $basesId;
    }
 
    /**
    * @depends  testKey
    */

    public function testdel($basesId)
    {
        
        list($prm,$idl) = $basesId;
        $i=0;
        
        $modS= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $modS->begin();
        
            $obj = $bd['Session']::getSession($idl[$i], [], 6000);
            $mod= $obj->getMod();
        
            $mod->delet();
            
            $this->assertEquals(0, $mod->getValN('ValidFlag'));
            
            $i++;
            $modS->end();
        }
        
        return $basesId;
    }
    
    /**
    * @depends  testdel
    */
    public function testdel2($basesId)
    {
        
        list($prm,$idl) = $basesId;
        $i=0;
        
        $modS= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $modS->begin();

            $obj = $bd['Session']::getSession($idl[$i], [], 6000);
            $key= $obj->getKey();
            $mod= $obj->getMod();
  
            $this->assertTrue($obj->isNew());
            $res=$mod->save();
            
            $this->assertEquals(2, $res);
            
            $x = Find::byKey($bd['Session'], 'BKey', $idl[$i]);
            $this->assertNull($x);
            
            $obj =$bd['Session']::getSession($key, [], 0);
            $this->assertTrue($obj->isNew());
            
            $x = Find::byKey($bd['Session'], 'BKey', $key);
            $this->assertNull($x);
            
            $i++;
            $modS->end();
        }
        
        return $basesId;
    }
}
