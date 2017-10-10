<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Controler;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\View\Vew;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';
$stypes = [CstMode::V_S_CREA,CstMode::V_S_READ, CstMode::V_S_UPDT];

class Controler_Perf_dataBase_User extends User
{
}

class Controler_Perf_dataBase_Role extends Role
{
}

class Controler_Perf_dataBase_Session extends Session
{
}

$numberRun=1;
$breath=20;
$depth=2;
//$bases= ['dataBase','memBase','fileBase'];
$bases =['dataBase'];
$size=10;
$cummulative=false;
$scenario = [
        CstMode::V_S_CREA,
        /*
		CstMode::V_S_READ, 
		CstMode::V_S_UPDT,     
		*/

];
$accessRight=2;

$numberRun=count($scenario)*$numberRun;
$runTimeList=[];
$numList=[];
$init=true;
$runTime=0;
$previousTime=0;
$currentTime=0;


foreach ($bases as $base) {
    $mes = 'non cumulative';
    if ($cummulative) {
        $mes='cumulative';
    }
    $mes2='witout access control';
    if ($accessRight==1) {
        $mes2="with Root access";
    }
    if ($accessRight==2) {
        $mes2="with User access";
    }
    echo "\nrunning on $base $mes $numberRun times with breath: $breath depth: $depth code: $size $mes2\n\n";
    $avg=0;
    $srun=0;
    for ($i = 0; $i < $numberRun; $i++) {
        $x= new Controler_Perf($accessRight);
        
        $s=$i % count($scenario);
        $sc = $scenario[$s];
        if (!$s) {
            $srun++;
        }
        $j=$s+1;
        
        if (!$s) {
            if ($i) {
                $x->close();
            }
            $x->reset();
            $runinit=($init || (! $cummulative));
            if ($runinit) {
                $x->initMod($accessRight, $size);
                $res= $x->initRoot();
            }
            $init=false;
        }
        
        $previousTime=xdebug_time_index();
        if ($sc == CstMode::V_S_CREA) {
            $n=$x->depthBreadthNew($res->getRPath(), $depth, $breath);
        }
        if ($sc == CstMode::V_S_READ) {
            $n=$x->depthRead($res->getRPath(), $depth+1);
        }
        if ($sc == CstMode::V_S_UPDT) {
            $n=$x->depthUpd($res->getRPath(), $depth+1);
        }
        $currentTime=xdebug_time_index();
        $runTime=$currentTime-$previousTime;
        $lavg = round($runTime/$n, 3);
        $runTimeList[$i]=$runTime;
        $numList[$i]=$n;
        $lrunTime= round($runTime, 3);
        if (!$s) {
            echo "scenario run $srun \n";
        }
        echo "\t step $j type : $sc \t number of object : $n \t time : $lrunTime \t average : $lavg\n";
        $avg=$avg+$runTime;
    }
    
    foreach ($stypes as $stype) {
        $aggrNum[$stype]=0;
        $aggrRunTime[$stype]=0;
    }
    for ($i = 0; $i < $numberRun; $i++) {
        $s=$i % count($scenario);
        $sc = $scenario[$s];
        $aggrNum[$sc]=$aggrNum[$sc]+$numList[$i];
        $aggrRunTime[$sc]=$aggrRunTime[$sc]+ $runTimeList[$i];
    }
    
    echo "\ntotals and averages on different runs \n";
    
    $j=1;
    $totalTime=0;
    $totalNum=0;
    foreach ($stypes as $stype) {
        if (in_array($stype, $scenario)) {
            $average= round($aggrRunTime[$stype]/$aggrNum[$stype], 3);
            $totalTime = $totalTime+$aggrRunTime[$stype];
            $totalNum=$totalNum+$aggrNum[$stype];
            $timeAggr = round($aggrRunTime[$stype], 3);
            echo "\t step type : $stype \t number of object : $aggrNum[$stype] \t time : $timeAggr \t average : $average \n";
        }
    }
    
    echo "\ntotals and averages on different types \n";
    
    $average=round($totalTime/$totalNum, 3);
    $totalTime=round($totalTime, 3);
    echo "\t step type : All \t number of object : $totalNum \t time : $totalTime \t average : $average \n";
}

class Controler_Perf
{
    
    public $config =  [
    'Handlers' => [],
    'Hdl'=> [],
    'Log'=> [],
    'Views' => []
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
    protected $baseTypes= ['dataBase'];
    protected $rootPath='/Controler_Test_1/1';
    protected $CName;
    protected $code;
    protected $cookieName;
    protected $ckey;
    
    public function __construct($accessRight)
    {
        $classes = ['Student','Code','Role','Session','User'];
        $prm=UtilsC::genPrm($classes, get_called_class(), $this->baseTypes);
        
        $this->config['Handlers']=$prm['handlers'];
        
        if ($accessRight != 0) {
            $usr=[
                    'Role' => $prm['dataBase']['Role'],
                    'User' => $prm['dataBase']['User'],
                    'Session' => $prm['dataBase']['Session'],
            ];
            $this->config['Hdl']= ['Usr'=>$usr];
        }
    }

    public function reset()
    {
        Log::reset();
        Mod::reset();
        Hdl::reset();
        Usr::reset();
        Adm::reset();
        Vew::reset();
    }
    
    public function initMod($accessRight, $size)
    {
       
        $classes = ['Student','Code',];
        $prm=UtilsC::genPrm($classes, get_called_class(), $this->baseTypes);
        $prm['application']= $this->ini;
        
        
        Mod::get()->init($prm['application'], $prm['handlers']);
        
        $this->CName=$prm['dataBase']['Student'];
        $this->code=$prm['dataBase']['Code'];

        
        $rolespec =[["true","true","true"]];
        $role1= json_encode($rolespec);
        
        $homep='|'.$this->CName;
        $home = $this->CName;

        $rolespec =[
                [[CstMode::V_S_SLCT],                     $homep,                             'true'],
                [[CstMode::V_S_READ],                     'true',                             'true'],
                [CstMode::V_S_UPDT,                       $homep,                             [$home=>':User<==>:User']],
                [CstMode::V_S_CREA,                       $homep,                             [$home=>':User<>:User']],
                [[CstMode::V_S_CREA,CstMode::V_S_UPDT],  [$homep.'|Cref'],                    [$home=>':User','Cref'=>':User']],
                [[CstMode::V_S_CREA,CstMode::V_S_UPDT],  [$homep.'|Cref|Cref'],               [$home=>':User','Cref'=>':User']],
                [[CstMode::V_S_CREA,CstMode::V_S_UPDT],  "|Session",                          ["Session"=>":id"]],
                [[CstMode::V_S_CREA,CstMode::V_S_UPDT],  "|User",                             ["User"=>":id<==>:User"]]
        ];
        $role2=json_encode($rolespec);

        Mod::get()->begin();
        
        //Users
        
        $classes = ['Session','User','Role',];
        $prm=UtilsC::genPrm($classes, get_called_class(), $this->baseTypes);
        $prm['application']= $this->ini;
            
        Mod::get()->init($prm['application'], $prm['handlers']);
            
        $res = ModUtils::initModBindings($prm['dataBase']);
            
        $role = $prm['dataBase']['Role'];
        $x = new Model($role);
        $x->setVal('Name', 'Default');
        $x->setVal('JSpec', $role1);
        if ($accessRight==2) {
            $x->setVal('JSpec', $role2);
        }
        $res=$x->save();
        echo $x->getErrLog()->show();
            
        $user = $prm['dataBase']['User'];
        $x = new Model($user);
        $x->setVal('UserId', 'test');
        $res=$x->save();
           
        $session = $prm['dataBase']['Session'];
        $x = new Model($session);
        $x->setVal('UserId', 'test');
        $x->setVal('RoleName', 'Default');
        $res=$x->save();
            
        $res=$x->save();
       
        $this->cookieName=$prm['application']['name'].$prm['dataBase']['Session'];
        $this->ckey=$x->getCobj()->getKey();
       
        //code
        
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
       
       // Student
       
        $x=new Model($this->CName);
        $x->getErrLog()->show();
        $x->deleteMod();
        $x->addAttr('Name', Mtype::M_STRING);
        $x->addAttr('Ref', Mtype::M_REF, '/'.$this->CName);
        $x->addAttr('User', Mtype::M_REF, '/'.$user);
        $x->addAttr('Cref', Mtype::M_CREF, '/'.$this->CName.'/Ref');
        if ($size > 0) {
            $x->addAttr('Code', Mtype::M_CODE, '/'.$this->code);
        }
        $x->saveMod();
        $x->getErrLog()->show();
       
       
        Mod::get()->end();
    }
    
    protected function ctrlrun()
    {
        Usr::reset();
        $ctrl = new Controler($this->config, $this->ini);
        $resc = $ctrl->run($this->show, 0);
        return $resc;
    }
    
    public function close()
    {
        Usr::reset();
        $ctrl = new Controler($this->config, $this->ini);
        $ctrl->close();
    }
    
    public function initRoot()
    {
        
        $path = '/';
        
        $_COOKIE[$this->cookieName]=$this->ckey;
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
    
    protected function createSon($path, $i)
    {
        $_COOKIE[$this->cookieName]=$this->ckey;
        
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
     

    public function depthBreadthNew($path, $n, $f)
    {
        $t = 0;
        if ($n==0) {
            return $t;
        }
        for ($i = 1; $i <= $f; $i++) {
            $name=$path.'_'.$i;
 //           echo "$path \n";
            $x=$this->createSon($path, $name);
            $t++;
            $t=$t+$this->depthBreadthNew($x->getRPath(), $n-1, $f);
        }
        return $t;
    }
 
    public function depthRead($path, $n)
    {
        $t=0;
        if ($n==0) {
            return $t;
        }
        $list = $this->getSonIds($path);
        $t++;
        foreach ($list as $id) {
            $sonPath=$path.'/Cref/'.$id;
            $t=$t+$this->depthRead($sonPath, $n-1);
        }
        return $t;
    }

       
    public function getSonIds($path)
    {
        $_COOKIE[$this->cookieName]=$this->ckey;
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;
        
        $handle = $this->ctrlrun();
        $sons = $handle->getVal('Cref');
        return $sons;
    }
    
    public function depthUpd($path, $n)
    {
        $t=0;
        if ($n==0) {
            return $t;
        }
        $list = $this->upd($path, $n);
        $t++;
        foreach ($list as $id) {
            $sonPath=$path.'/Cref/'.$id;
            $t=$t+$this->depthUpd($sonPath, $n-1);
        }
        return $t;
    }
    
    protected function upd($path, $i)
    {
        $_COOKIE[$this->cookieName]=$this->ckey;
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_READ;
        
        $handle = $this->ctrlrun();
        $sons = $handle->getVal('Cref');

        
        $_GET['Action']=CstMode::V_S_UPDT;
        
        $this->ctrlrun();
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Name']=$i;
        
        $res=$this->ctrlrun();
        
        return $sons;
    }
        
    public function select($path, $i)
    {
        $_COOKIE[$this->cookieName]=$this->ckey;
        $_SERVER['REQUEST_METHOD']='GET';
        $_SERVER['PATH_INFO']=$path;
        $_GET['Action']=CstMode::V_S_SLCT;
        
        $this->ctrlrun();
        
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST['Ref']=$i;
        
        $this->ctrlrun();
    }
    

    protected function del($path)
    {
        $_COOKIE[$this->cookieName]=$this->ckey;
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
