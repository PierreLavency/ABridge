<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\Controler;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\View\Vew;

use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Usr\User;

class Controler_Test_dataBase_User extends User
{
}

class Controler_Test_dataBase_Role extends Role
{
}

class Controler_Test_dataBase_Session extends Session
{
}

class Controler_Test_dataBase_Admin extends Admin
{
}

class Controler_Test extends PHPUnit_Framework_TestCase
{
    

    protected $show = false;
    
    protected function reset()
    {
        Log::reset();
        Mod::reset();
        Hdl::reset();
        Usr::reset();
        Adm::reset();
        Vew::reset();
    }
       
    public function testInit()
    {
        $classes = ['Student'];
        $baseTypes=['dataBase'];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        $this->reset();
        
        Mod::get()->init($prm['application'], $prm['handlers']);
        
        Mod::get()->begin();
        
        $name = $prm['dataBase']['Student'];
        
        $x=new Model($name);
        $x->deleteMod();
        $x->addAttr('Name', Mtype::M_INT);
        $x->addAttr('Ref', Mtype::M_REF, '/'.$name);
        $x->addAttr('Cref', Mtype::M_CREF, '/'.$name.'/Ref');
        $x->saveMod();
        
        $classes = ['Session','User','Role',];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
            
        Mod::get()->init($prm['application'], $prm['handlers']);
        
        $res = ModUtils::initModBindings($prm['dataBase']);
        
        $role = $prm['dataBase']['Role'];
        $x = new Model($role);
        $x->setVal('Name', 'Default');
        $x->setVal('JSpec', json_encode([["true","true","true"]]));
        $res=$x->save();
        $this->assertEquals(1, $res);
        
        $user = $prm['dataBase']['User'];
        $x = new Model($user);
        $x->setVal('UserId', 'test');
        $res=$x->save();
        $this->assertEquals(1, $res);
        
        $session = $prm['dataBase']['Session'];
        $x = new Model($session);
        $x->setVal('UserId', 'test');
        $x->setVal('RoleName', 'Default');
        $res=$x->save();
        $this->assertEquals(1, $res);
        
        $res=$x->save();
        $this->assertFalse($x->isErr());
           
        
        Mod::get()->end();
        
        $classes=['Session','User','Role','Student'];
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        $prm['key']=$x->getCobj()->getKey();
        
        $config =
        [
                'Default'  => [],
                'Apps'=> [],
                'Handlers' => [
                        $name=>['dataBase'],
                        'Controler_Test_dataBase_Admin'=>['memBase'],
                ],
                'Hdl' => [
                        'Usr'=>['Session'=>$session,'User'=>$user,'Role'=>$role]
                ],
                'Log' => [],
                'Adm' => [Adm::ADMIN=>'Controler_Test_dataBase_Admin'],
                'View'=> [],
        ];
        
        
        $cookiename = $prm['application']['name'].$session;
        $prm['cookieName']=$cookiename;
        $prm['config']=$config;
        $prm['rootPath']='/'.$name;
        return $prm;
    }
    
    /**
     * @depends testInit
     */
    
    function testRoot($prm)
    {
        $this->reset();
        $ctrl = new Controler($prm['config'], $prm['application']);
       
        $path = '/';
        $_COOKIE[$prm['cookieName']]=$prm['key'];
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;
        
        $resc = $ctrl->run($this->show);
        
        $this->assertTrue($resc->nullobj());
        
        
        $path=$prm['rootPath'];

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_CREA;
        
        $resc = $ctrl->run($this->show);
 
        $this->assertFalse($resc->isErr());
        $this->assertEquals(CstMode::V_S_CREA, $resc->getAction());
        $this->assertEquals(0, $resc->getId());
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=0;
        
        $res = $ctrl->run($this->show);

        $this->assertEquals($res->getId(), 1);
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$res->getRPath();
        unset($_GET['Action']);
    
        $reso = $ctrl->run($this->show);
        
        $this->assertEquals($res->getUrl(), $reso->getUrl());
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $reso->getAction());
        $this->assertEquals($reso->getId(), 1);

        return $prm;
    }

    /**
    * @depends testRoot
    */
    
    public function testRootErr($prm)
    {
        $this->reset();
        $ctrl = new Controler($prm['config'], $prm['application']);
        
        $_COOKIE[$prm['cookieName']]=$prm['key'];
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']= $prm['rootPath'].'/1';
        $_GET['Action']=CstMode::V_S_UPDT;
        $_POST['Name']='a';

        $res = $ctrl->run($this->show);

        $this->assertEquals($res->getVal('Name'), 0);
        $this->assertTrue($res->isErr());
        $this->assertEquals(CstMode::V_S_UPDT, $res->getAction());
        
        return $prm;
    }
    /**
    * @depends testRootErr
    */
    
    public function testSelect($prm)
    {
        $this->reset();
        $ctrl = new Controler($prm['config'], $prm['application']);
        
        $_COOKIE[$prm['cookieName']]=$prm['key'];
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$prm['rootPath'];
        $_GET['Action']=CstMode::V_S_SLCT;
        $_POST['Name']=0;
        $_POST['Name_OP']='=';
        
        $reso = $ctrl->run($this->show);
        $this->assertEquals(1, count($reso->select()));
        return $prm;
    }

    /**
    * @depends testRoot
    */
    
    public function testNewSon($prm)
    {
 
        $prm['rootPath']=$prm['rootPath'].'/1';
        
        $res = $this->newSon($prm);
        
        $this->assertEquals($res->getVal('Name'), 1);
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
  
        $prm['sonPath']=$res->getRpath();
        return $prm;
    }

    
    private function newSon($prm)
    {
        $this->reset();
        $ctrl = new Controler($prm['config'], $prm['application']);
        
        $fpath = $prm['rootPath'].'/Cref';
        
        $_COOKIE[$prm['cookieName']]=$prm['key'];
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['Action']=CstMode::V_S_CREA;
        
        $res = $ctrl->run($this->show);

        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=1;
        
        $res = $ctrl->run($this->show);
             
        return $res;
    }
    
    /**
    * @depends testNewSon
    */
    
    public function testUpdSon($prm)
    {
        $this->reset();
        $ctrl = new Controler($prm['config'], $prm['application']);
        
        $path=$prm['sonPath'];

        $_COOKIE[$prm['cookieName']]=$prm['key'];
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;

        $res = $ctrl->run($this->show);
        
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
    
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_UPDT;
        
        $res = $ctrl->run($this->show);
        
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_UPDT, $res->getAction());
 
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=2;
        $_POST['vnum']=1;
        
        $res = $ctrl->run($this->show);

        $this->assertEquals($res->getVal('Name'), 2);
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());

        return $prm;
    }
    
    /**
    * @depends testUpdSon
    */
    
    public function testDelSon($prm)
    {
        $path=$prm['sonPath'];
        $r= $this->delPath($prm, $path);
        $x=$r[1];
        $res=$r[0];
        
        $this->assertEquals($x, $res->getRpath());
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
        
        return $prm;
    }
    
    /**
     * @depends testDelSon
     */
    
    public function testDelRoot($prm)
    {
        $path=$prm['rootPath'];
        $r= $this->delPath($prm, $path);
        $x=$r[1];
        $res=$r[0];
        
        $this->assertEquals($x, '/');

        $this->assertTrue($res->nullObj());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
        $this->assertEquals('/', $res->getRPath());
        
        $res= $res->getDD();
        $this->assertTrue($res->nullObj());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
        $this->assertEquals('/', $res->getRPath());
        
        return $prm;
    }
    
    
    /**
     * @depends testDelRoot
     */
    
    public function testClose($prm)
    {
        $this->reset();
        $ctrl = new Controler($prm['config'], $prm['application']);
        $this->assertTrue($ctrl->close());
    }

    private function delPath($prm, $path)
    {
        $this->reset();
        $ctrl = new Controler($prm['config'], $prm['application']);

        $_COOKIE[$prm['cookieName']]=$prm['key'];
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;

        $res = $ctrl->run($this->show);
        
        $x=$res->getDD()->getRPath();
            
        $_GET['Action']=CstMode::V_S_DELT;

        $res = $ctrl->run($this->show);
            
        $_SERVER['REQUEST_METHOD']='POST';
        $res = $ctrl->run($this->show);
        
        return [$res,$x];
    }
}
