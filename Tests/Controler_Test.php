<?php

use ABridge\ABridge\Controler;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\View\Vew;


use ABridge\ABridge\Hdl\CstMode;


use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

class Controler_Test extends PHPUnit_Framework_TestCase
{
    
    protected $config =  [
    'Handlers' => [
            'Controler_Test_1'=>['dataBase'],
            'Controler_Test_2'=>['fileBase'],
    ],

    'View' => [
        'Home'=>['Controler_Test_1'],
        'Controler_Test_1' =>[
                'attrList' => [
                    CstView::V_S_REF => ['id'],
                    ],
                'attrHtml' => [
                    CstMode::V_S_READ => ['Name'=>CstHTML::H_T_PLAIN],
                ],
                'attrProp' => [
                    CstMode::V_S_SLCT =>[CstView::V_P_LBL,CstView::V_P_OP,CstView::V_P_VAL],
                ],
                'navList' => [CstMode::V_S_READ => [CstMode::V_S_UPDT,CstMode::V_S_SLCT],
                ],
                'lblList' => [
                    'id'        => 'Noma',
                ],
            ],
        ]
    ];
    protected $show = false;
    protected $rootPath='/Controler_Test_1/1';
    protected $CName='Controler_Test_1';

    protected static $prm;
    
    public static function setUpBeforeClass()
    {
                    
        self::$prm=
        [
                'name'=>'test',
                'base'=>'dataBase',
                'dataBase'=>'test_'.__CLASS__,
                'fileBase'=>'test_'.__CLASS__,
                'memBase' =>'test_'.__CLASS__,
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];
    }
    
    function testRoot()
    {
        Log::reset();
        Mod::reset();
        Hdl::reset();
        Usr::reset();
        Adm::reset();
        Vew::reset();
            
        $ctrl = new Controler($this->config, self::$prm);
        
        $x=new Model($this->CName);
        $x->deleteMod();
        $x=new Model($this->CName);
        $x->addAttr('Name', Mtype::M_INT);
        $x->addAttr('Ref', Mtype::M_REF, '/'.$this->CName);
        $x->addAttr('Cref', Mtype::M_CREF, '/'.$this->CName.'/Ref');
        $x->saveMod();
        
        $path = '/';
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;

        
        $resc = $ctrl->run($this->show, 0);


        $this->assertTrue($resc->nullobj());

        $path='/'.$this->CName;

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_CREA;
        
        $resc = $ctrl->run($this->show, 0);
 
        $this->assertFalse($resc->isErr());
        $this->assertEquals(CstMode::V_S_CREA, $resc->getAction());
        $this->assertEquals($resc->getId(), 0);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=0;
        
        $res = $ctrl->run($this->show, 0);

        $this->assertEquals($res->getId(), 1);
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$res->getRPath();
        unset($_GET['Action']);
    
        $reso = $ctrl->run($this->show, 0);
        
        $this->assertEquals($res->getUrl(), $reso->getUrl());
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $reso->getAction());
        $this->assertEquals($reso->getId(), 1);

        return $reso->getRPath();
        
        $ctrl->close();
    }

    /**
    * @depends testRoot
    */
    
    public function testRootErr($path)
    {
        $ctrl = new Controler($this->config, self::$prm);

        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_UPDT;
        $_POST['Name']='a';


        $res = $ctrl->run($this->show, 0);


        $this->assertEquals($res->getVal('Name'), 0);
        $this->assertTrue($res->isErr());
        $this->assertEquals(CstMode::V_S_UPDT, $res->getAction());
    }
    /**
    * @depends testRootErr
    */
    
    public function testSelect()
    {
        $ctrl = new Controler($this->config, self::$prm);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']='/'.$this->CName;
        $_GET['Action']=CstMode::V_S_SLCT;
        $_POST['Name']=0;
        $_POST['Name_OP']='=';
        
        $reso = $ctrl->run($this->show, 0);
        $this->assertEquals(1, count($reso->select()));
    }

    /**
    * @depends testRoot
    */
    
    public function testNewSon($path)
    {
 
        $res = $this->newSon($path);
        
        $this->assertEquals($res->getVal('Name'), 1);
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
  
        return $res->getRpath();
    }

    
    private function newSon($path)
    {
        $ctrl = new Controler($this->config, self::$prm);
        $fpath = $path.'/Cref';

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['Action']=CstMode::V_S_CREA;
        
        $res = $ctrl->run($this->show, 0);

        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=1;
        
        $res = $ctrl->run($this->show, 0);
        
        return $res;
    }
    
    /**
    * @depends testNewSon
    */
    
    public function testUpdSon($path)
    {
        $ctrl = new Controler($this->config, self::$prm);

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;

        $res = $ctrl->run($this->show, 0);
        
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
    
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_UPDT;
        
        $res = $ctrl->run($this->show, 0);
        
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_UPDT, $res->getAction());
 
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=2;
        
        $res = $ctrl->run($this->show, 0);

        $this->assertEquals($res->getVal('Name'), 2);
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());

        return $res->getRPath();
    }
    
    /**
    * @depends testUpdSon
    */
    
    public function testDelSon($path)
    {
        $r= $this->delSon($path);
        $x=$r[1];
        $res=$r[0];
        
        $this->assertEquals($x, $res->getRpath());
        $this->assertFalse($res->isErr());
        $this->assertEquals(CstMode::V_S_READ, $res->getAction());
        
        return $x;
    }
    
    private function delSon($path)
    {
        $ctrl = new Controler($this->config, self::$prm);
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;

        $res = $ctrl->run($this->show, 0);
        
        $x=$res->getDD()->getRPath();
            
        $_GET['Action']=CstMode::V_S_DELT;

        $res = $ctrl->run($this->show, 0);
            
        $_SERVER['REQUEST_METHOD']='POST';
        $res = $ctrl->run($this->show, 0);
        
        return [$res,$x];
    }
    
    /**
    * @depends testDelSon
    */
    
    public function testDepthNew($path)
    {
        $x=$path;
        $date = new DateTime();
        $s=$date->getTimestamp();
        for ($i = 1; $i <= 15; $i++) {
            $x=$this->NewSon($x);
            $x=$x->getRPath();
        }
        $date = new DateTime();
        $ss= $date->getTimestamp();
//		echo $ss-$s; echo "\n";
        return $x;
    }


    /**
    * @depends testDepthNew
    */
    
    public function testDepthDel($path)
    {
        $x=$path;
        $date = new DateTime();
        $s=$date->getTimestamp();
        for ($i = 1; $i <= 15; $i++) {
            $x=$this->delSon($x);
            $x=$x[0];
            $x=$x->getRpath();
        }
        $date = new DateTime();
        $ss= $date->getTimestamp();
//		echo $ss-$s; echo "\n";

        return $x;
    }
}
