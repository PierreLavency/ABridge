<?php

use ABridge\ABridge\Controler;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\CstMode;
use ABridge\ABridge\View\CstView;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';


$x= new Controler_Perf();
$x->initMod();
$res= $x->initRoot();
/*
$x->depthNew($res->getRPath(),30);
echo xdebug_time_index(), "\n";
$x->breadthNew($res->getRPath(),30);
echo xdebug_time_index(), "\n";
*/

$x->depthBreadthNew($res->getRPath(), 2, 20);
echo xdebug_time_index(), "\n";

class Controler_Perf
{
    
    protected $config =  [
    'Handlers' => [
    'Controler_Test_1'=>['dataBase','test_perf'],
    'Controler_Test_2'=>['fileBase'],
    ],
    'Views' => [
        'Home'=>['Controler_Test_1'],
        'Controler_Test_1' =>[
                'attrList' => [
                    CstView::V_S_REF         => ['id'],
                    ],

                'attrProp' => [
                    CstMode::V_S_SLCT =>[CstView::V_P_LBL,CstView::V_P_OP,CstView::V_P_VAL],
                ],
            ],
        ]
    ];
    
    protected $ini = [
            'name'=>'UnitTest',
            'path'=>'C:/Users/pierr/ABridge/Datastore/',
            'host'=>'localhost',
            'user'=>'cl822',
            'pass'=>'cl822',
    ];
    
    protected $show = false;
    protected $rootPath='/Controler_Test_1/1';
    protected $CName='Controler_Test_1';

    
    protected function ctrlrun()
    {
        $ctrl = new Controler($this->config, $this->ini);
        $resc = $ctrl->run($this->show, 0);
//    	$resc->getErrLog()->show();
        $ctrl->close();
        return $resc;
    }
        
    public function initMod()
    {
        $ctrl = new Controler($this->config, $this->ini);
        $ctrl->beginTrans();
        
        $x=new Model($this->CName);
        $x->deleteMod();
        $x=new Model($this->CName);
        $x->addAttr('Name', Mtype::M_STRING);
        $x->addAttr('Ref', Mtype::M_REF, '/'.$this->CName);
        $x->addAttr('Cref', Mtype::M_CREF, '/'.$this->CName.'/Ref');
        $x->saveMod();
 
        $ctrl->commit();
        $ctrl->close();
    }
    
    public function initRoot()
    {
        $path = '/';
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;
        
        $this->ctrlrun();
        
        $path='/'.$this->CName;

        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_CREA;
        
        $this->ctrlrun();
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=0;
        
        $res=$this->ctrlrun();
        
        return $res;
    }

    /**
    * @depends testRoot
    */
    
    public function select($path, $i)
    {
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_SLCT;

        $this->ctrlrun();
        
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Ref']=$i;

        $this->ctrlrun();
    }

 
    protected function createSon($path, $i)
    {
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;
        
        $this->ctrlrun();
        
        $fpath = $path.'/Cref';
        $_SERVER['PATH_INFO']=$fpath;
        $_GET['Action']=CstMode::V_S_CREA;
        
        $this->ctrlrun();

        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=$i;
        
        $res= $this->ctrlrun();
        
        return $res;
    }
     
    public function depthNew($path, $n)
    {
        $x=$path;
        for ($i = 1; $i <= $n; $i++) {
            $x=$this->createSon($x, $i);
            $x=$x->getRPath();
        }
        return $x;
    }

    public function breadthNew($path, $n)
    {
        $x=$path;
        for ($i = 1; $i <= $n; $i++) {
            $this->createSon($x, $i);
        }
        return $x;
    }
    
    public function depthBreadthNew($path, $n, $f)
    {
        if ($n==0) {
            return;
        }
        for ($i = 1; $i <= $f; $i++) {
            $name=$path.'_'.$i;
            $x=$this->createSon($path, $name);
            $this->depthBreadthNew($x->getRPath(), $n-1, $f);
        }
        return;
    }
    
    protected function Upd($path, $i)
    {
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;
        
        $this->ctrlrun();
        
        $_GET['Action']=CstMode::V_S_UPDT;
        
        $this->ctrlrun();
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=$i;
        
        $res=$this->ctrlrun();
        
        return $res;
    }
    
    
    protected function del($path)
    {
        
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;
        
        $this->ctrlrun();
        
        $_GET['Action']=CstMode::V_S_DELT;
        
        $this->ctrlrun();
        
        $_SERVER['REQUEST_METHOD']='POST';
        
        $this->ctrlrun();
    }
}
