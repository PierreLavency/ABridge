<?php

use ABridge\ABridge\UtilsC;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\ModUtils;

use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Hdl\CstMode;


use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Distribution;
use ABridge\ABridge\Usr\Session;

class Access_Test_dataBase_User extends User
{
}
class Access_Test_fileBase_User extends User
{
}

class Access_Test_dataBase_Role extends Role
{
}
class Access_Test_fileBase_Role extends Role
{
}

class Access_Test_dataBase_Session extends Session
{
}
class Access_Test_fileBase_Session extends Session
{
}

class Access_Test_dataBase_Distribution extends Distribution
{
}
class Access_Test_fileBase_Distribution extends Distribution
{
}

class Access_Test extends PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $classes = ['Session','User','Role','Distribution'];
        
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
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            
            $x = new Model($bd['Role']);
            $x->setVal('Name', 'Default');
            $res=$x->save();
            $this->assertEquals(1, $res);
 
            $x = new Model($bd['Role']);
            $x->setVal('Name', 'Defaults');
            $res=$x->save();
            $this->assertEquals(2, $res);
 
            $x = new Model($bd['Role']);
            $x->setVal('Name', 'Errors');
            $res=$x->save();
            $this->assertEquals(3, $res);
            
            $x = new Model($bd['User']);
            $x->setVal('UserId', 'test');
            $res=$x->save();
            $this->assertEquals(1, $res);

            $x = new Model($bd['User']);
            $x->setVal('UserId', 'test2');
            $res=$x->save();
            $this->assertEquals(2, $res);

            $x = new Model($bd['User']);
            $x->setVal('UserId', 'test3');
            $res=$x->save();
            $this->assertEquals(3, $res);
            
            $x = new Model($bd['Distribution']);
            $x->setVal('Role', 1);
            $x->setVal('User', 1);
            $res=$x->save();
            $this->assertEquals(1, $res);

            $x = new Model($bd['Distribution']);
            $x->setVal('Role', 2);
            $x->setVal('User', 2);
            $res=$x->save();
            $this->assertEquals(2, $res);

            $x = new Model($bd['Distribution']);
            $x->setVal('Role', 3);
            $x->setVal('User', 3);
            $res=$x->save();
            $this->assertEquals(3, $res);
            
            $x = new Model($bd['Session']);
            $x->setVal('UserId', 'test');
            $x->setVal('RoleName', 'Default');
            $res=$x->save();
            $this->assertEquals(1, $res);
            $res=$x->save();
            $this->assertFalse($x->isErr());
                        
            $x = new Model($bd['Session']);
            $x->setVal('UserId', 'test2');
            $x->setVal('RoleName', 'Defaults');
            $res=$x->save();
            $this->assertEquals(2, $res);
            $res=$x->save();
            $this->assertFalse($x->isErr());

            
            $x = new Model($bd['Session']);
            $res=$x->save();
            $this->assertEquals(3, $res);
            $this->assertFalse($x->isErr());

            $x = new Model($bd['Session']);
            $x->setVal('UserId', 'test3');
            $x->setVal('RoleName', 'Errors');
            $res=$x->save();
            $this->assertEquals(4, $res);
            $res=$x->save();
            $this->assertFalse($x->isErr());
            
            
            $mod->end();
        }
        return $prm;
    }
    
    /**
     * @dataProvider Provider1
     * @depends testsave
     */
    public function testCond($p, $b, $c, $e1, $prm)
    {
        
        $rolespec =[
        [[CstMode::V_S_READ,CstMode::V_S_SLCT],      'true',                                'true'],
        [CstMode::V_S_SLCT,                         '|User',                                'false'],
        [CstMode::V_S_READ,                         '|User',                                ["User"=>":User"]],
        [CstMode::V_S_UPDT,                         '|Application',                         ["Application"=>":User"]],
        [CstMode::V_S_DELT,                         '|Application',                         ['Application'=>':User<!=>:User']],
        [[CstMode::V_S_CREA,CstMode::V_S_UPDT,CstMode::V_S_DELT], ['|Application|In','|Application|Out'], ["Application"=>":User"]],
        [[CstMode::V_S_CREA,CstMode::V_S_DELT],      '|Application|BuiltFrom',               ["Application"=>":User","BuiltFrom"=>":User"]],
        ];

        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
        
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
            
            $mod->end();
        }
        return $prm;
    }

    public function Provider1()
    {
        return [
            ['/Userr/1',                    CstMode::V_S_UPDT, 'User'        ,false],
            ['/User',                       CstMode::V_S_SLCT, 'User'        ,false],
            ['/User/1',                     CstMode::V_S_READ, 'User'        ,[':User']],
            ['/Application',                CstMode::V_S_SLCT, 'Application' ,true],
            ['/Application/1',              CstMode::V_S_UPDT, 'Application' ,[':User']],
            ['/Application/1',              CstMode::V_S_DELT, 'Application' ,[':User<!=>:User']],
            ['/Application/1/In',           CstMode::V_S_CREA, 'Application' ,[':User']],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, 'Application' ,[':User']],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, 'BuiltFrom'   ,[':User']],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, 'User'        ,true],
            ['/Application/1/Ins',          CstMode::V_S_CREA, 'Application' ,false],
            ['/Application/1/Ins',          CstMode::V_S_SLCT, 'Application' ,true],
            ];
    }
    
    /**
     * @dataProvider Provider2
     * @depends testsave
     */
    
    public function testCheck($p, $b, $e1, $e2, $e3, $prm)
    {
        
        $rolespec =[
        [[CstMode::V_S_READ,CstMode::V_S_SLCT],  'true',                                 'true'],
        [CstMode::V_S_UPDT,                      '|Application',                         ['Application'=>':User<>:User']],
        [CstMode::V_S_DELT,                       '|Application',                        ['Application'=>':User<!=>:User']],
        [[CstMode::V_S_CREA,CstMode::V_S_UPDT],  ['|Application|In','Application|Out'],  ['Application'=>':User']],
        [[CstMode::V_S_CREA],                    '|Application|BuiltFrom',               ['Application'=>':User','BuiltFrom'=>':User']],
        ];
        
        $mod= Mod::get();

        foreach ($prm['bindL'] as $bd) {
            $mod->begin();

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
            
            
            $mod->end();
        }
        return $prm;
    }

    public function Provider2()
    {
        return [
            ['/ApplicationA/1',             CstMode::V_S_UPDT, false, false, false],
            ['/Application',                CstMode::V_S_SLCT, true, true , true],
            ['/Application/1',              CstMode::V_S_UPDT, true, false, false],
            ['/Application/1',              CstMode::V_S_DELT, false,true, true],
            ['/Application/1/In',           CstMode::V_S_CREA, true, false, true],
            ['/Application/1/BuiltFrom',    CstMode::V_S_CREA, true, false, true],
            ['/Application/1/Ins',          CstMode::V_S_CREA, false,false, false],
            ['/Application/1/Ins',          CstMode::V_S_SLCT, true, true, true],
            ];
    }

    /**
     * @dataProvider Provider3
     * @depends testsave
     */
    
    public function testDefault($p, $b, $e1, $prm)
    {
        
        
        $mod= Mod::get();

        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
                    
            $y = new Model($bd['Session'], 3);
            $r = $y->getCobj();
                    
            $req = new Request($p, $b);
            
            $res = $r->checkARight($req, [['Session',$y]], true);
            $this->assertEquals($e1, $res);
            
            
            
            $mod->end();
        }
        return $prm;
    }
    
    public function Provider3()
    {
        return [
                ['/',             CstMode::V_S_READ, true],
                ['/Session/3',    CstMode::V_S_READ, true],
                ['/Session/3',    CstMode::V_S_UPDT, true],
                ['/Application/3',CstMode::V_S_UPDT, false],
        ];
    }
    
    /**
     * @dataProvider Provider4
     * @depends testsave
     */
    
    public function testErr2($p, $b, $e1, $prm)
    {
        $rolespec =[
                [CstMode::V_S_DELT, '|Application',['Application'=>':User<eroor>:User']],
                [CstMode::V_S_UPDT, '|Application',['Application'=>'<>:User']],
                [CstMode::V_S_READ, '|Application',['Application'=>':User<eroor>>:User']],
                [CstMode::V_S_SLCT, '|Application',['Application'=>'User<==>:User']],
        ];
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $mod->begin();
            $role = new Model($bd['Role'], 3);
            $res = json_encode($rolespec);
            $role->setVal('JSpec', $res);
            $role->save();
            
            $y = new Model($bd['Session'], 4);
            $r = $y->getCobj();
            
            $x = new Model('TestApp');
            $x->addAttr('User', Mtype::M_INTP);
            $x->setVal('User', 2);
            
            $req = new Request($p, $b);
            $res = "";
            try {
                $res = $r->checkARight($req, [['Application',$x],['BuiltFrom',$x]], true);
            } catch (Exception $e) {
                $res= $e->getMessage();
            }
            $this->assertEquals($e1, $res);
        }
    }
    
    public function Provider4()
    {
        return [
                ['/Application/1', CstMode::V_S_DELT,  CstError::E_ERC066.':eroor'],
                ['/Application/1', CstMode::V_S_UPDT,  CstError::E_ERC065.':<>:User'],
                ['/Application/1', CstMode::V_S_READ,  CstError::E_ERC065.'::User<eroor>>:User'],
                ['/Application', CstMode::V_S_SLCT,    CstError::E_ERC051.':User'],
        ];
    }
    
    /**
     * @depends testsave
     */
        
    public function testErr($prm)
    {

        
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
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

            try {
                $x=$r->getAttrPathVal(null, ':x:x');
            } catch (Exception $e) {
                $res= $e->getMessage();
            }
            $this->assertEquals($res, CstError::E_ERC050);
            
            $mod->end();
        }
        return $prm;
    }
    
    /**
     * @dataProvider Provider5
     * @depends testErr
     */
    
    public function testPath($p, $e1, $prm)
    {
        $mod= Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            $y = new Model($bd['Session'], 1);
            $r = $y->getCobj();
            
            try {
                $res=$r->getAttrPathVal($y, $p);
            } catch (Exception $e) {
                $res= $e->getMessage();
            }

            $this->assertEquals($e1, $res);
                        
            
            $mod->end();
        }
    }
    public function Provider5()
    {
        return [
                [':User:UserId','test'],
                ['const','const'],
                [':',  CstError::E_ERC051.'::'],

        ];
    }
}
