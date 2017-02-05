<?php


require_once("Model.php"); 
require_once("Controler.php"); 

class Controler_Test extends PHPUnit_Framework_TestCase  
{
    
    protected $config =  [
	'Handlers' => ['tclass'=>['dataBase','test'],],
	'Home'=>['tclass']
	];
    protected $show = false;
	protected $path;
    
    function testNew()
    {

        $ctrl = new Controler($this->config);
        
        $x=new Model('tclass');
        $x->deleteMod();
        $x=new Model('tclass');
        $x->addAttr('Name',M_INT);
        $x->addAttr('Ref',M_REF,'/tclass');
        $x->addAttr('Cref',M_CREF,'/tclass/Ref');
        
        $x->saveMod();
        
        $this->path='/tclass';

        $_SERVER['REQUEST_METHOD']='GET';   
        $_SERVER['PATH_INFO']=$this->path;
        $_GET['View']=V_S_CREA;
        
        $ctrl = new Controler($this->config);
        $resc = $ctrl->run($this->show,0);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$this->path;
        $_POST['action']=V_S_CREA;
        $_POST['Name']=1;
        
        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,0);

        $ctrl = new Controler($this->config);
        $this->assertNotNull($x=new Model('tclass',1));
        $this->assertEquals($x->getVal('Name'),1);
        
		$x=$this->path.'/1';
        $this->path = $x;
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$this->path;
		unset($_GET['View']);
        
        $reso = $ctrl->run($this->show,0);
        
        $this->assertEquals($resc.'/1',$reso);

		
		$fpath = '/tclass/1/Cref';
		
        $_SERVER['REQUEST_METHOD']='GET';   
        $_SERVER['PATH_INFO']=$fpath;
        
        $ctrl = new Controler($this->config);
        $resc = $ctrl->run($this->show,0);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$fpath;
        $_POST['action']=V_S_CREA;
        $_POST['Name']=1;
        
        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,0);
        
        $ctrl = new Controler($this->config);
        $this->assertNotNull($x=new Model('tclass',2));
        $this->assertEquals($x->getVal('Name'),1);
        
        $fpath = $fpath.'/2';
    
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['View']=V_S_UPDT;
        
        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,0);
        
        $this->assertEquals($resc.'/2',$res);
        

        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$fpath;
        $_POST['action']=V_S_UPDT;
        $_POST['Name']=2;
        
        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,0);

        $ctrl = new Controler($this->config);
        $this->assertNotNull($x=new Model('tclass',2));
        $this->assertEquals($x->getVal('Name'),2);
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['View']=V_S_READ;
        
        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,0);
            
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['View']=V_S_DELT;
        
        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,0);

        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$fpath;
        $_POST['action']=V_S_DELT;
        
        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,0);

        $this->assertEquals($reso,$res);
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_SERVER['PATH_INFO']=$this->path;
        $_POST['action']=V_S_UPDT;
        $_POST['Name']='a';

        $ctrl = new Controler($this->config);
        $res = $ctrl->run($this->show,2);
        $this->expectOutputString(
"<br>LINE:0<br>SELECT * FROM tclass where id= 1<br>LINE:1<br> **************  <br>LINE:2<br>SELECT id FROM tclass where Ref= '1'<br><br>"   
        );
        $ctrl = new Controler($this->config);       
        $this->assertNotNull($x=new Model('tclass',1));
        $this->assertEquals($x->getVal('Name'),1);
        
        
    }
}


