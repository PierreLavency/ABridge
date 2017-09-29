<?php

use ABridge\ABridge\Controler;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\View\Vew;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';


$numberRun=2;
$breath=20;
$depth=2;
//$bases= ['dataBase','memBase','fileBase'];
$bases =['dataBase'];
$size=20;
$cummulative=false;

$init=true;
$runTime=0;
$previousTime=0;
$currentTime=0;
foreach ($bases as $base) {
    $mes = 'non cumulative';
    if ($cummulative) {
        $mes='cumulative';
    }
    echo "\nrunning on $base $mes $numberRun times with breath: $breath depth: $depth code: $size\n";
    $avg=0;
    for ($i = 0; $i < $numberRun; $i++) {
        $x= new Controler_Perf();
        $x->config['Handlers']=['Controler_Test_1'=>[$base],'Controler_Test_2'=>[$base]];
        $runinit=($init || (! $cummulative));
        $x->initMod($runinit, $size);
        $init=false;
        if ($runinit) {
            $res= $x->initRoot();
        }

        $n=$x->depthBreadthNew($res->getRPath(), $depth, $breath);

        $x->close();
        $currentTime=xdebug_time_index();
        $runTime=$currentTime-$previousTime;
        $previousTime=$currentTime;
        $j=$i+1;
        $lavg = round($runTime/$n, 3);
        $lrunTime= round($runTime, 3);
        echo "\t run $j : $lrunTime average : $lavg\n";
        $avg=$avg+$runTime;
    }
    $avg=round($avg/$numberRun, 3);
    echo "number of objects/run : $n   \n";
    echo "average run time      : $avg \n" ;
    $avg=round($avg/$n, 3);
    echo "average time/object   : $avg \n";
    $tn = $n;
    if ($cummulative) {
        $tn = $n * $numberRun;
    }
    echo "number of objects at end  : $tn \n";
}

class Controler_Perf
{
    
    public $config =  [
    'Handlers' => [],
    'Log'=>[],
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
    
    private $ini = [
            'name'=>'test_perf',
            'base'=>'dataBase',
            'dataBase'=>'test_perf',
            'fileBase'=>'test_perf',
            'memBase '=>'test_perf',
            'path'=>'C:/Users/pierr/ABridge/Datastore/',
            'host'=>'localhost',
            'user'=>'cl822',
            'pass'=>'cl822',
            'trace'=>'0',

 //   		'tclass'=>'ABridge\ABridge\Controler',
            'tfunction'=>'getObj',
            'tdisp'=>'1',
 //  		'tline'=>'443',

    ];
    
    protected $show = false;
    protected $rootPath='/Controler_Test_1/1';
    protected $CName='Controler_Test_1';
    protected $code= 'Controler_Test_2';
    
    protected function ctrlrun()
    {
        $ctrl = new Controler($this->config, $this->ini);
        $resc = $ctrl->run($this->show, 0);
        return $resc;
    }
        
    public function initMod($init, $size)
    {

        Log::reset();
        Mod::reset();
        Hdl::reset();
        Usr::reset();
        Adm::reset();
        Vew::reset();
    
        $ctrl = new Controler($this->config, $this->ini);
        if ($init) {
            Mod::get()->begin();
            
            if ($size > 0) {
                $x=new Model($this->code);
                $x->deleteMod();
                $x->addAttr('Name', Mtype::M_STRING);
                $x->saveMod();
                $x->getErrLog()->show();
                for ($i=0; $i<$size; $i++) {
                    $x = new Model($this->code);
                    $x->setVal('Name', (string) $i);
                    $x->save();
                }
            }
            $x=new Model($this->CName);
            $x->getErrLog()->show();
            $x->deleteMod();
            
            $x->addAttr('Name', Mtype::M_STRING);
            $x->addAttr('Ref', Mtype::M_REF, '/'.$this->CName);
            $x->addAttr('Cref', Mtype::M_CREF, '/'.$this->CName.'/Ref');
            if ($size > 0) {
                $x->addAttr('Code', Mtype::M_CODE, '/'.$this->code);
            }
            $x->saveMod();
            
            Mod::get()->end();
        }
    }
    
    
    public function close()
    {
        $ctrl = new Controler($this->config, $this->ini);
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
        $t = 0;
        if ($n==0) {
            return $t;
        }
        for ($i = 1; $i <= $f; $i++) {
            $name=$path.'_'.$i;
            $x=$this->createSon($path, $name);
            $t++;
            $t=$t+$this->depthBreadthNew($x->getRPath(), $n-1, $f);
        }
        return $t;
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
