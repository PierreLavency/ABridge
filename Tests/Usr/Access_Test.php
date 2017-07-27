<?php

use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;

use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Hdl\CstMode;


use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Distribution;
use ABridge\ABridge\Usr\Session;

class Access_Test_dataBase_2 extends User
{
}
class Access_Test_fileBase_2 extends User
{
}

class Access_Test_dataBase_3 extends Role
{
}
class Access_Test_fileBase_3 extends Role
{
}

class Access_Test_dataBase_1 extends Session
{
}
class Access_Test_fileBase_1 extends Session
{
}

class Access_Test_dataBase_4 extends Distribution
{
}
class Access_Test_fileBase_4 extends Distribution
{
}

class Access_Test extends PHPUnit_Framework_TestCase
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
        $classes = ['Session','User','Role','Distribution'];
        $bsname = get_called_class();
        $bases = UtilsC::initHandlers($name, $classes, $bsname, $prm);
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
 
            $x = new Model($bd['Role']);
            $x->setVal('Name', 'Defaults');
            $res=$x->save();
            $this->assertEquals(2, $res);
 
            $x = new Model($bd['User']);
            $x->setVal('UserId', 'test');
            $res=$x->save();
            $this->assertEquals(1, $res);

            $x = new Model($bd['User']);
            $x->setVal('UserId', 'test2');
            $res=$x->save();
            $this->assertEquals(2, $res);
                    
            $x = new Model($bd['Distribution']);
            $x->setVal('ofRole', 1);
            $x->setVal('toUser', 1);
            $res=$x->save();
            $this->assertEquals(1, $res);

            $x = new Model($bd['Distribution']);
            $x->setVal('ofRole', 2);
            $x->setVal('toUser', 2);
            $res=$x->save();
            $this->assertEquals(2, $res);
            
            $x = new Model($bd['Session']);
            $x->setVal('UserId', 'test');
            $x->setVal('Role', 1);
            $res=$x->save();
            $this->assertEquals(1, $res);
  
            $x = new Model($bd['Session']);
            $x->setVal('UserId', 'test2');
            $x->setVal('Role', 2);
            $res=$x->save();
            $this->assertEquals(2, $res);
  
            $db->commit();
        }
        
        return $bases;
    }
    
    /**
     * @dataProvider Provider1
     * @depends testsave
     */
    public function testCond($p, $b, $c, $e1, $bases)
    {
        
        $rolespec =[
        [[CstMode::V_S_READ,CstMode::V_S_SLCT],      'true',                                 'true'],
        [CstMode::V_S_SLCT,                         '|User',                                'false'],
        [CstMode::V_S_READ,                         '|User',                                ["User"=>"User"]],
        [CstMode::V_S_UPDT,                         '|Application',                         ["Application"=>"User"]],
        [[CstMode::V_S_CREA,CstMode::V_S_UPDT,CstMode::V_S_DELT], ['|Application|In','|Application|Out'], ["Application"=>"User"]],
        [[CstMode::V_S_CREA,CstMode::V_S_DELT],      '|Application|BuiltFrom',               ["Application"=>"User","BuiltFrom"=>"User"]],
        ];

        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
        
            $role = new Model($bd['Role'], 1);
            $res = json_encode($rolespec);
            $role->setVal('JSpec', $res);
            $role->save();

            $y = new Model($bd['Session'], 1);
            $x = $y->getCobj();

            $req= new Request($p, $b);
     
            if ($e1) {
                $this->assertTrue($x->checkReq($req));
            } else {
                $this->assertFalse($x->checkReq($req));
            }
            
            $db->commit();
        }
        return $bases;
    }

    public function Provider1()
    {
        return [
            ['/Userr/1',                    CstMode::V_S_UPDT, 'User'        ,false],
            ['/User',                       CstMode::V_S_SLCT, 'User'        ,false],
            ['/User/1',                     CstMode::V_S_READ, 'User'        ,['User']],
            ['/Application',                CstMode::V_S_SLCT, 'Application' ,['true']],
            ['/Application/1',              CstMode::V_S_UPDT, 'Application' ,['User']],
            ['/Application/1/In',           CstMode::V_S_CREA, 'Application' ,['User']],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, 'Application' ,['User']],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, 'BuiltFrom'   ,['User']],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, 'User'        ,['true']],
            ['/Application/1/Ins',          CstMode::V_S_CREA, 'Application' ,false],
            ['/Application/1/Ins',          CstMode::V_S_SLCT, 'Application' ,['true']],
            ];
    }
    
    /**
     * @dataProvider Provider2
     * @depends testsave
     */
    
    public function testCheck($p, $b, $e1, $e2, $e3, $bases)
    {
        
        $rolespec =[
        [[CstMode::V_S_READ,CstMode::V_S_SLCT],           'true',                                 'true'],
        [CstMode::V_S_UPDT,                      '|Application',                         ['Application'=>'User<>User']],
        [[CstMode::V_S_CREA,CstMode::V_S_UPDT,CstMode::V_S_DELT],  ['|Application|In','Application|Out'],  ['Application'=>'User']],
        [[CstMode::V_S_CREA,CstMode::V_S_DELT],           '|Application|BuiltFrom',               ['Application'=>'User','BuiltFrom'=>'User']],
        ];
        
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();

            $role = new Model($bd['Role'], 2);
            $res = json_encode($rolespec);
            $role->setVal('JSpec', $res);
            $role->save();

            $y = new Model($bd['Session'], 2);
            $r = $y->getCobj();
                
            $x = new Model('TestApp');
            $x->addAttr('User', Mtype::M_INTP);
            $x->setVal('User', 2);

            $req = new Request($p, $b);
                
            $res = $r->checkARight($req, [['Application',$x],['BuiltFrom',$x]], true);
            $this->assertEquals($e1, $res);
        
            $x = new Model('TestApp');
            $x->addAttr('User', Mtype::M_INTP);
            $x->setVal('User', 1);
        
            $res = $r->checkARight($req, [['Application',$x]], true);
            $this->assertEquals($e2, $res);
        
            $x = new Model('TestApp');
            $x->addAttr('User', Mtype::M_INTP);
            
            $res = $r->checkARight($req, [['Application',$x]], true);
            $this->assertEquals($e3, $res);
            
            $db->commit();
        }
        return $bases;
    }

    public function Provider2()
    {
        return [
            ['/ApplicationA/1',             CstMode::V_S_UPDT, false, false, false],
            ['/Application',                CstMode::V_S_SLCT, true, true , true],
            ['/Application/1',              CstMode::V_S_UPDT, true, false, false],
            ['/Application/1/In',           CstMode::V_S_CREA, true, false, true],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, true, false, true],
            ['/Application/1/Ins',          CstMode::V_S_CREA, false,false, false],
            ['/Application/1/Ins',          CstMode::V_S_SLCT, true, true, true],
            ];
    }

    /**
     * @depends testsave
     */
        
    public function testErr($bases)
    {
 
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();
        
            $y = new Model($bd['Session'], 1);
            $r = $y->getCobj();

            $res = "";
            try {
                $x=$r->checkreq(null);
            } catch (Exception $e) {
                $res= $e->getMessage();
            }
            $this->assertEquals($res, CstError::E_ERC012);
            
            $res = "";
            try {
                $x=$r->checkARight(null, [], true);
            } catch (Exception $e) {
                $res= $e->getMessage();
            }
            $this->assertEquals($res, CstError::E_ERC012);

        
            $db->commit();
        }
        return $bases;
    }
    
    /**
     * @dataProvider Provider1
     */
    public function itestRoot($p, $b, $c, $e1)
    {
        $y = new SessionHdl();
        $req = new Request($p, $b);
        $r = $y->getReqCond($req, $c);
        $this->assertEquals(['true'], $r);
        
        $y = new SessionHdl(null, null);
        $r = $y->getReqCond($req, $c);
        $this->assertEquals(['true'], $r);
        
        $s = new Model('TestSess');
        $s->addAttr('User', Mtype::M_INT);
        
        $y = new SessionHdl($s, null);
        $r = $y->getReqCond($req, $c);
        $this->assertEquals(['true'], $r);

        $y = new SessionHdl(null);
        $r = $y->getReqCond($req, $c);
        $this->assertEquals(['true'], $r);

        
        $this->assertTrue($y->isRoot());
    }
}
