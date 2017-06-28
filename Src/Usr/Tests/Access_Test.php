<?php

require_once 'UtilsC.php';
require_once 'CModel.php';
require_once 'Model.php';
require_once 'Handler.php';
require_once '/Usr/Src/User.php';
require_once '/Usr/Src/Role.php';
require_once '/Usr/Src/Distribution.php';
require_once '/Usr/Src/Session.php';

    
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
        $name = 'test';
        $classes = ['Session','User','Role','Distribution'];
        $bsname = get_called_class();
        $bases = UtilsC::initHandlers($name, $classes, $bsname);
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
        [[V_S_READ,V_S_SLCT],           'true',                                 'true'],
        [V_S_SLCT,                      '|User',                                'false'],
        [V_S_READ,                      '|User',                                ["User"=>"User"]],
        [V_S_UPDT,                      '|Application',                         ["Application"=>"User"]],
        [[V_S_CREA,V_S_UPDT,V_S_DELT], ['|Application|In','|Application|Out'], ["Application"=>"User"]],
        [[V_S_CREA,V_S_DELT],           '|Application|BuiltFrom',               ["Application"=>"User","BuiltFrom"=>"User"]],
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
            ['/Userr/1',                    V_S_UPDT, 'User'        ,false],
            ['/User',                       V_S_SLCT, 'User'        ,false],
            ['/User/1',                     V_S_READ, 'User'        ,['User']],
            ['/Application',                V_S_SLCT, 'Application' ,['true']],
            ['/Application/1',              V_S_UPDT, 'Application' ,['User']],
            ['/Application/1/In',           V_S_CREA, 'Application' ,['User']],
            ['/Application/1/BuiltFrom',    V_S_CREA, 'Application' ,['User']],
            ['/Application/1/BuiltFrom',    V_S_CREA, 'BuiltFrom'   ,['User']],
            ['/Application/1/BuiltFrom',    V_S_CREA, 'User'        ,['true']],
            ['/Application/1/Ins',          V_S_CREA, 'Application' ,false],
            ['/Application/1/Ins',          V_S_SLCT, 'Application' ,['true']],
            ];
    }
    
    /**
     * @dataProvider Provider2
     * @depends testsave
     */
    
    public function testCheck($p, $b, $e1, $e2, $e3, $bases)
    {
        
        $rolespec =[
        [[V_S_READ,V_S_SLCT],           'true',                                 'true'],
        [V_S_UPDT,                      '|Application',                         ['Application'=>'User<>User']],
        [[V_S_CREA,V_S_UPDT,V_S_DELT],  ['|Application|In','Application|Out'],  ['Application'=>'User']],
        [[V_S_CREA,V_S_DELT],           '|Application|BuiltFrom',               ['Application'=>'User','BuiltFrom'=>'User']],
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
            $x->addAttr('User', M_INTP);
            $x->setVal('User', 2);

            $req = new Request($p, $b);
                
            $res = $r->checkARight($req, [['Application',$x],['BuiltFrom',$x]], true);
            $this->assertEquals($e1, $res);
        
            $x = new Model('TestApp');
            $x->addAttr('User', M_INTP);
            $x->setVal('User', 1);
        
            $res = $r->checkARight($req, [['Application',$x]], true);
            $this->assertEquals($e2, $res);
        
            $x = new Model('TestApp');
            $x->addAttr('User', M_INTP);
            
            $res = $r->checkARight($req, [['Application',$x]], true);
            $this->assertEquals($e3, $res);
            
            $db->commit();
        }
        return $bases;
    }

    public function Provider2()
    {
        return [
            ['/ApplicationA/1',             V_S_UPDT, false, false, false],
            ['/Application',                V_S_SLCT, true, true , true],
            ['/Application/1',              V_S_UPDT, true, false, false],
            ['/Application/1/In',           V_S_CREA, true, false, true],
            ['/Application/1/BuiltFrom',    V_S_CREA, true, false, true],
            ['/Application/1/Ins',          V_S_CREA, false,false, false],
            ['/Application/1/Ins',          V_S_SLCT, true, true, true],
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
            $this->assertEquals($res, E_ERC012);
            
            $res = "";
            try {
                $x=$r->checkARight(null, [], true);
            } catch (Exception $e) {
                $res= $e->getMessage();
            }
            $this->assertEquals($res, E_ERC012);

        
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
        $s->addAttr('User', M_INT);
        
        $y = new SessionHdl($s, null);
        $r = $y->getReqCond($req, $c);
        $this->assertEquals(['true'], $r);

        $y = new SessionHdl(null);
        $r = $y->getReqCond($req, $c);
        $this->assertEquals(['true'], $r);

        
        $this->assertTrue($y->isRoot());
    }
}
