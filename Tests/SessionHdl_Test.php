<?php
    

require_once 'SessionHdl.php' ;

class SessionHdl_Test extends PHPUnit_Framework_TestCase
{

    
    /**
     * @dataProvider Provider1
     */
    public function testCond($p, $b, $c, $e1)
    {
        $rolespec =[
        [[V_S_READ,V_S_SLCT],           'true',                                 'true'],
        [V_S_SLCT,                      '|User',                                'false'],
        [V_S_READ,                      '|User',                                ["User"=>"User"]],
        [V_S_UPDT,                      '|Application',                         ["Application"=>"User"]],
        [[V_S_CREA,V_S_UPDT,V_S_DELT],  ['|Application|In','|Application|Out'],     ["Application"=>"User"]],
        [[V_S_CREA,V_S_DELT],           '|Application|BuiltFrom',               ["Application"=>"User","BuiltFrom"=>"User"]],
        ];

        $role = new Model('RoleSess');
        $role->addAttr('JSpec', M_STRING);
        $res = json_encode($rolespec);
        $role->setVal('JSpec', $res);

        $y = new Model('TestSess');
        $y->addAttr('User', M_INT);
        $y->setVal('User', 1);
        
        $x = new SessionHdl($y, $role);
        $req= new Request($p, $b);
        
        $r = $x->getReqCond($req, $c);
        $this->assertEquals($e1, $r);
        if ($r) {
            $this->assertTrue($x->checkReq($req));
        } else {
            $this->assertFalse($x->checkReq($req));
        }
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
     * @dataProvider Provider1
     */
    public function testRoot($p, $b, $c, $e1)
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
    
    /**
     * @dataProvider Provider2
     */
    
    public function testCheck($p, $b, $e1, $e2, $e3)
    {
        $rolespec =[
        [[V_S_READ,V_S_SLCT],           'true',                                 'true'],
        [V_S_UPDT,                      '|Application',                         ['Application'=>'User<>User']],
        [[V_S_CREA,V_S_UPDT,V_S_DELT],  ['|Application|In','Application|Out'],  ['Application'=>'User']],
        [[V_S_CREA,V_S_DELT],           '|Application|BuiltFrom',               ['Application'=>'User','BuiltFrom'=>'User']],
        ];
        
        $role = new Model('RoleSess');
        $role->addAttr('JSpec', M_STRING);
        $js=json_encode($rolespec);

        $role->setVal('JSpec', $js);
    
        $y = new Model('TestSess');
        $y->addAttr('User', M_INT);
        $y->setVal('User', 1);
        
        $r = new SessionHdl($y, $role);
                
        $x = new Model('TestApp');
        $x->addAttr('User', M_INT);
        $x->setVal('User', 1);

        $req = new Request($p, $b);
                
        $res = $r->checkARight($req, [['Application',$x],['BuiltFrom',$x]], true);
        $this->assertEquals($e1, $res);
        
        $x = new Model('TestApp');
        $x->addAttr('User', M_INT);
        $x->setVal('User', 2);
        
        $res = $r->checkARight($req, [['Application',$x]], true);
        $this->assertEquals($e2, $res);
        
        $x = new Model('TestApp');
        $x->addAttr('User', M_INT);
        
        $res = $r->checkARight($req, [['Application',$x]], true);
        $this->assertEquals($e3, $res);
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

    public function testErr()
    {
        $role = new Model('RoleSess');
        $role->addAttr('JSpec', M_STRING);

    
        $y = new Model('TestSess');
        $y->addAttr('User', M_INT);
        $y->setVal('User', 1);
        
        $r = new SessionHdl($y, $role);

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
        
        
        $x = new Model('TestApp');
        $x->addAttr('User', M_INT);
        $x->setVal('User', 1);
    }
}
