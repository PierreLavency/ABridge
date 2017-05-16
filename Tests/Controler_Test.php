<?php

require_once("Model.php");
require_once("Controler.php");

class Controler_Test extends PHPUnit_Framework_TestCase
{
    
    protected $config =  [
    'Handlers' => [
    'Controler_Test_1'=>['dataBase','testb'],
    'Controler_Test_2'=>['fileBase'],
    ],
    'Home'=>['Controler_Test_1'],
    'Views' => [
        'Controler_Test_1' =>[
                'attrList' => [
                    V_S_REF         => ['id'],
                    ],
                'attrHtml' => [
                    V_S_READ => ['Name'=>H_T_PLAIN],
                ],
                'attrProp' => [
                    V_S_SLCT =>[V_P_LBL,V_P_OP,V_P_VAL],
                ],
                'navList' => [V_S_READ => [V_S_UPDT,V_S_SLCT],
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

    function testRoot()
    {
        $ctrl = new Controler($this->config, [
        'name'=>'UnitTest',
        'path'=>'C:/Users/pierr/ABridge/Datastore/',
        'host'=>'localhost',
        'user'=>'cl822',
        'pass'=>'cl822',
        ]);
        
        $x=new Model($this->CName);
        $x->deleteMod();
        $x=new Model($this->CName);
        $x->addAttr('Name', M_INT);
        $x->addAttr('Ref', M_REF, '/'.$this->CName);
        $x->addAttr('Cref', M_CREF, '/'.$this->CName.'/Ref');
        $x->saveMod();
        
        $path = '/';
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=V_S_READ;
        
        $resc = $ctrl->run($this->show, 2);
        $this->expectOutputString(
            "uri is /ABridge.php<br>path is /<br>method is GET<br><br>LINE:0<br> **************  <br><br><br>LINE:0<br> **************  <br><br>"
        );

        
        $this->assertTrue($resc->nullobj());
 
        $path='/'.$this->CName;

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=V_S_CREA;
        
        $resc = $ctrl->run($this->show, 0);
 
        $this->assertFalse($resc->isErr());
        $this->assertEquals(V_S_CREA, $resc->getAction());
        $this->assertEquals($resc->getId(), 0);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=0;
        
        $res = $ctrl->run($this->show, 0);

        $this->assertEquals($res->getId(), 1);
        $this->assertFalse($res->isErr());
        $this->assertEquals(V_S_READ, $res->getAction());
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$res->getRPath();
        unset($_GET['Action']);
    
        $reso = $ctrl->run($this->show, 0);
        
        $this->assertEquals($res->getUrl(), $reso->getUrl());
        $this->assertFalse($res->isErr());
        $this->assertEquals(V_S_READ, $reso->getAction());
        $this->assertEquals($reso->getId(), 1);

        return $reso->getRPath();
        
        $ctrl->close();
    }

    /**
    * @depends testRoot
    */
    
    public function testRootErr($path)
    {
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);

        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=V_S_UPDT;
        $_POST['Name']='a';


        $res = $ctrl->run($this->show, 0);


        $this->assertEquals($res->getVal('Name'), 0);
        $this->assertTrue($res->isErr());
        $this->assertEquals(V_S_UPDT, $res->getAction());
    }
    /**
    * @depends testRootErr
    */
    
    public function testSelect()
    {
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']='/'.$this->CName;
        $_GET['Action']=V_S_SLCT;
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
        $this->assertEquals(V_S_READ, $res->getAction());
  
        return $res->getRpath();
    }

    
    private function newSon($path)
    {
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $fpath = $path.'/Cref';

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['Action']=V_S_CREA;
        
        $res = $ctrl->run($this->show, 0);

        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=1;
        
        $res = $ctrl->run($this->show, 0);
        
        $ctrl->close();
        return $res;
    }
    
    /**
    * @depends testNewSon
    */
    
    public function testUpdSon($path)
    {
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=V_S_READ;

        $res = $ctrl->run($this->show, 0);
        
        $this->assertFalse($res->isErr());
        $this->assertEquals(V_S_READ, $res->getAction());
    
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=V_S_UPDT;
        
        $res = $ctrl->run($this->show, 0);
        
        $this->assertFalse($res->isErr());
        $this->assertEquals(V_S_UPDT, $res->getAction());
 
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=2;
        
        $res = $ctrl->run($this->show, 0);

        $this->assertEquals($res->getVal('Name'), 2);
        $this->assertFalse($res->isErr());
        $this->assertEquals(V_S_READ, $res->getAction());

        $ctrl->close();
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
        $this->assertEquals(V_S_READ, $res->getAction());
        
        return $x;
    }
    
    private function delSon($path)
    {
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=V_S_READ;

        $res = $ctrl->run($this->show, 0);
        
        $x=$res->getDD()->getRPath();
            
        $_GET['Action']=V_S_DELT;

        $res = $ctrl->run($this->show, 0);
            
        $_SERVER['REQUEST_METHOD']='POST';
        $res = $ctrl->run($this->show, 0);
        
        $ctrl->close();
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
