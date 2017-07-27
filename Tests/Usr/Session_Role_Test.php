<?php

use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;

use ABridge\ABridge\Hdl\CstMode;


use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Session;

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
        $prm=[
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];
        $name = 'test';
        $classes = ['Session','Role'];
        $bsname = get_called_class();
        $bases= UtilsC::initHandlers($name, $classes, $bsname, $prm);
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
 
            $sesshdl = $x->getCobj();
            $this->assertNotNull($sesshdl->getKey());
 
            $res=$x->save();
            $this->assertEquals(1, $res);
 
            $x = new Model($bd['Role'], 1);
            $x->setVal('Name', 'Defaults');

            $res=$x->save();
            
            $x = new Model($bd['Session']);
            
            $res=$x->save();
            $this->assertEquals(2, $res);
 
 
            $db->commit();
        }
        
        return $bases;
    }

    /**
    * @depends  testsave
    */
    
    public function testGet($bases)
    {
        $rolespec =[
        [[CstMode::V_S_READ,CstMode::V_S_SLCT],           'true',                                 'true'],
        [CstMode::V_S_SLCT,                      '|User',                                'false'],
        [CstMode::V_S_READ,                      '|User',                                ["User"=>"User"]],
        [CstMode::V_S_UPDT,                      '|Application',                         ["Application"=>"User"]],
        [[CstMode::V_S_CREA,CstMode::V_S_UPDT,CstMode::V_S_DELT], ['|Application|In','|Application|Out'], ["Application"=>"User"]],
        [[CstMode::V_S_CREA,CstMode::V_S_DELT],           '|Application|BuiltFrom',               ["Application"=>"User","BuiltFrom"=>"User"]],
        ];
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
            
            $x = new Model($bd['Session'], 1);

            $res= $x->getVal('Role');
            
            $this->assertEquals(1, $res);

            $sessionHdl = $x->getCobj();
            $this->assertNull($sessionHdl->getRSpec());

            $res = $sessionHdl->getObj('Role');
            $this->assertEquals(1, $res->getId());

            $x = new Model($bd['Role'], 1);
            $x->setVal('JSpec', json_encode($rolespec));
            $x->save();

            
            $x = new Model($bd['Session'], 2);
            $res= $x->getVal('Role');
            $this->assertNull($res);

            $sessionHdl = $x->getCobj();
            $this->assertNull($sessionHdl->getRSpec());

            $res = $sessionHdl->getObj($bd['Session']);
            $this->assertEquals(2, $res->getId());
            
            $db->commit();
        }
        
        return $bases;
    }
 
     /**
    * @depends  testGet
    */
    
    public function testMenu($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();

            $x = new Model($bd['Session'], 1);
            $sessionHdl = $x->getCobj();
            $res= $sessionHdl->getSelMenu(['User','Application']);
            $this->assertEquals(['/Application'], $res);
            
            $db->commit();
        }
        return $bases;
    }
}
