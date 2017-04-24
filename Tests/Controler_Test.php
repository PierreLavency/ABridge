<?php

require_once("Model.php"); 
require_once("Controler.php"); 

class Controler_Test extends PHPUnit_Framework_TestCase  
{
    
    protected $config =  [
	'Handlers' => ['Controler_Test_1'=>['dataBase','test'],],
	'Home'=>['Controler_Test_1']
	];
    protected $show = false;
	protected $path;
    
    function testNew()
    {

        $ctrl = new Controler($this->config,['name'=>'UnitTest']);
        
        $x=new Model('Controler_Test_1');
        $x->deleteMod();
        $x=new Model('Controler_Test_1');
        $x->addAttr('Name',M_INT);
        $x->addAttr('Ref',M_REF,'/Controler_Test_1');
        $x->addAttr('Cref',M_CREF,'/Controler_Test_1/Ref');
        
        $x->saveMod();
        
        $this->path='/Controler_Test_1';

        $_SERVER['REQUEST_METHOD']='GET';   
        $_SERVER['PATH_INFO']=$this->path;
        $_GET['Action']=V_S_CREA;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $resc = $ctrl->run($this->show,0);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$this->path;
        $_POST['Action']=V_S_CREA;
        $_POST['Name']=1;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,0);

        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $this->assertNotNull($x=new Model('Controler_Test_1',1));
        $this->assertEquals($x->getVal('Name'),1);
        
		$x=$this->path.'/1';
        $this->path = $x;
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$this->path;
		unset($_GET['Action']);
        
        $reso = $ctrl->run($this->show,0);
        
        $this->assertEquals($resc.'/1',$reso);

		
		$fpath = '/Controler_Test_1/1/Cref';
		
        $_SERVER['REQUEST_METHOD']='GET';   
        $_SERVER['PATH_INFO']=$fpath;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $resc = $ctrl->run($this->show,0);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$fpath;
        $_POST['Action']=V_S_CREA;
        $_POST['Name']=1;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,0);
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $this->assertNotNull($x=new Model('Controler_Test_1',2));
        $this->assertEquals($x->getVal('Name'),1);
        
        $fpath = $fpath.'/2';
    
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['Action']=V_S_UPDT;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,0);
        
        $this->assertEquals($resc.'/2',$res);
        

        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$fpath;
        $_POST['Action']=V_S_UPDT;
        $_POST['Name']=2;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,0);

        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $this->assertNotNull($x=new Model('Controler_Test_1',2));
        $this->assertEquals($x->getVal('Name'),2);
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['Action']=V_S_READ;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,0);
            
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['Action']=V_S_DELT;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,0);

        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$fpath;
        $_POST['Action']=V_S_DELT;
        
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,0);

        $this->assertEquals($reso,$res);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$this->path;
        $_POST['Action']=V_S_UPDT;
        $_POST['Name']='a';

        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);
        $res = $ctrl->run($this->show,2);
        $this->expectOutputString(
"<br>LINE:0<br>SELECT * FROM Controler_Test_1 where id= 1<br>LINE:1<br> **************  <br>LINE:2<br>SELECT id FROM Controler_Test_1 where Ref= '1'<br><br>"   
        );
        $ctrl = new Controler($this->config, ['name'=>'UnitTest']);       
        $this->assertNotNull($x=new Model('Controler_Test_1',1));
        $this->assertEquals($x->getVal('Name'),1);
        
        
    }
}


