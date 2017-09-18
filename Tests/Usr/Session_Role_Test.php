<?php

use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\CstError;

use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Session;

class Session_Role_Test_dataBase_Role extends Role
{
};
class Session_Role_Test_fileBase_Role extends Role
{
};

class Session_Role_Test_dataBase_Session extends Session
{
};
class Session_Role_Test_fileBase_Session extends Session
{
};


class Session_Role_Test extends PHPUnit_Framework_TestCase
{
   
    public function testInit()
    {
        $classes = ['Session','Role'];
        
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        Mod::reset();
        
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

            $x = new Model($bd['Role']);
            $x->setVal('Name', 'Default');

            $res=$x->save();
            $this->assertEquals(1, $res);
            
            $x = new Model($bd['Session']);
 
            $sesshdl = $x->getCobj();
            $this->assertNotNull($sesshdl->getKey());
 
            $res=$x->save();
            $this->assertEquals(1, $res);
            $res=$x->save();
            $this->assertFalse($x->isErr());
            
            
            $x = new Model($bd['Role'], 1);
            $x->setVal('Name', 'Defaults');

            $res=$x->save();
            
            $x = new Model($bd['Session']);
            
            $res=$x->save();
            $this->assertEquals(2, $res);
            $res=$x->save();
            $this->assertEquals(CstError::E_ERC064.":".$bd['Role'], $x->getErrLine());
            
            $x->setVal('RoleName', 'NotExists');
            $x->save();
            $this->assertEquals(CstError::E_ERC059.":".$bd['Role'].":NotExists", $x->getErrLine());
            
 
            $mod->end();
        }
        
        return $prm;
    }

    /**
    * @depends  testsave
    */
    
    public function testGet($prm)
    {
        $rolespec =[
        [[CstMode::V_S_READ,CstMode::V_S_SLCT],           'true',                                 'true'],
        [CstMode::V_S_SLCT,                      '|User',                                'false'],
        [CstMode::V_S_READ,                      '|User',                                ["User"=>"User"]],
        [CstMode::V_S_UPDT,                      '|Application',                         ["Application"=>"User"]],
        [[CstMode::V_S_CREA,CstMode::V_S_UPDT,CstMode::V_S_DELT], ['|Application|In','|Application|Out'], ["Application"=>"User"]],
        [[CstMode::V_S_CREA,CstMode::V_S_DELT],           '|Application|BuiltFrom',               ["Application"=>"User","BuiltFrom"=>"User"]],
        ];
        
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd['Session'], 1);

            $res= $x->getVal('ActiveRole');
            
            $this->assertEquals(1, $res);

            $sessionHdl = $x->getCobj();
            $this->assertNull($sessionHdl->getRSpec());

            $res = $sessionHdl->getObj('ActiveRole');
            $this->assertEquals(1, $res->getId());

            $x = new Model($bd['Role'], 1);
            $x->setVal('JSpec', json_encode($rolespec));
            $x->save();

            
            $x = new Model($bd['Session'], 2);
            $res= $x->getVal('ActiveRole');
            $this->assertNull($res);

            $sessionHdl = $x->getCobj();
            $this->assertNull($sessionHdl->getRSpec());

            $res = $sessionHdl->getObj($bd['Session']);
            $this->assertEquals(2, $res->getId());
            
            $mod->end();
        }
        
        return $prm;
    }
 
     /**
    * @depends  testGet
    */
    
    public function testMenu($prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();

            $x = new Model($bd['Session'], 1);
            $sessionHdl = $x->getCobj();
            $res= $sessionHdl->getSelMenu(['User','Application']);
            $this->assertEquals(['/Application'], $res);
            
            $mod->end();
        }
        return $prm;
    }
}
