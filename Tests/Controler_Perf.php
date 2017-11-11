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
use ABridge\ABridge\Log\Logger;
use phpDocumentor\Reflection\Types\Boolean;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';


class Controler_Perf_dataBase_User extends User
{
}

class Controler_Perf_dataBase_Role extends Role
{
}

class Controler_Perf_dataBase_Session extends Session
{
}


class Controler_Perf_memBase_User extends User
{
}

class Controler_Perf_memBase_Role extends Role
{
}

class Controler_Perf_memBase_Session extends Session
{
}


class Controler_Perf_fileBase_User extends User
{
}

class Controler_Perf_fileBase_Role extends Role
{
}

class Controler_Perf_fileBase_Session extends Session
{
}
$stypes = [CstMode::V_S_CREA,CstMode::V_S_READ, CstMode::V_S_UPDT];

$conf = parse_ini_file($home.'/Tests/perf.ini');

$numberRun=(int) $conf['numberRun'];
$breath=(int) $conf['breath'];
$depth=(int) $conf['depth'];
$size=(int) $conf['size'];
$accessRight=(int) $conf['accessRight'];

$cummulative=(Boolean) $conf['cummulative'];
$initP=(Boolean) $conf['initP'];
$saveLog=(Boolean) $conf['saveLog'];

$scenarioNum=(int) $conf['scenarioNum'];
$baseNum=(int) $conf['baseNum'];


$scenarioList=[
        [CstMode::V_S_CREA,CstMode::V_S_READ,CstMode::V_S_UPDT,],
        [CstMode::V_S_CREA,],
        ];

$baseList = [
        ['dataBase',],
        ['dataBase','fileBase',],
        ['dataBase','fileBase','memBase'],
];

$scenario = $scenarioList[$scenarioNum];
$bases= $baseList[$baseNum];

$decodebs = ['dataBase'=>'db','fileBase'=>'fb','memBase'=>'mb'];
$bsName = $decodebs[$bases[0]];
$i=0;
foreach ($bases as $base) {
    if ($i) {
        $bsName = $bsName.'+'.$decodebs[$base];
    }
    $i++;
}

$decodesc = [CstMode::V_S_CREA=>'C',CstMode::V_S_READ=>'R',CstMode::V_S_UPDT=>'U',CstMode::V_S_DELT=>'D',CStMode::V_S_SLCT=>'S'];
$scName=$decodesc[$scenario[0]];
$i=0;
foreach ($scenario as $step) {
    if ($i) {
        $scName = $scName.'-'.$decodesc[$step];
    }
    $i++;
}
$scName=$scName.' '.$depth.':'.$breath.':'.$size.' ';

$decodear = ['NA','Root','Usr'];
if ($accessRight==1) {
    $scName=$scName.'R';
}
if ($accessRight==2) {
    $scName=$scName.'U';
}

$lgName= $bsName." $numberRun* ".$scName;
echo $lgName . "\n";

$log = new Logger();

foreach ($bases as $base) {
    $rpath='/Controler_Perf_'.$base.'_Student/1';
    
    $runTimeList=[];
    $numList=[];
    $runTime=0;
    $previousTime=0;
    $currentTime=0;
    $init = $initP;
    
    $mes = 'non cumulative';
    if ($cummulative) {
        $mes='cumulative';
    }
    $mes2='Access Rights: Disabled';
    if ($accessRight==1) {
        $mes2='Access Rights: Root';
    }
    if ($accessRight==2) {
        $mes2='Access Rights: User';
    }
    $Nstep=count($scenario);
    echo "\nrunning on $base $mes $numberRun times $Nstep step secenario with breath: $breath depth: $depth code: $size $mes2\n\n";
    $numberRunTot=count($scenario)*$numberRun;
    $avg=0;
    $srun=0;
    $x= new Controler_Perf($accessRight, $base);
    if (!$init and $accessRight) {
        $x->setKey();
    }
    for ($i = 0; $i < $numberRunTot; $i++) {
        $s=$i % count($scenario);
        $sc = $scenario[$s];
        if (!$s) {
            $srun++;
        }
        $j=$s+1;
        
        if (!$s) {
            if ($base != 'memBase') {
                if ($i) {
                    $x->close();
                }
                $x->reset();
            }
            $runinit=($init || (! $cummulative));
            if ($runinit) {
                $x->initMod($accessRight, $size);
                $x->initRoot();
            }
            $init=false;
        }
        
        $previousTime=xdebug_time_index();
        if ($sc == CstMode::V_S_CREA) {
            $n=$x->depthBreadthNew($rpath, $depth, $breath);
        }
        if ($sc == CstMode::V_S_READ) {
            $n=$x->depthRead($rpath, $depth+1);
        }
        if ($sc == CstMode::V_S_UPDT) {
            $n=$x->depthUpd($rpath, $depth+1);
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
        $log->logLine('', ['Breath'=>$breath,'Depth'=>$depth,'Code'=>$size,'Operation'=>$sc,'Number'=>$n, 'Time'=>$lrunTime,'Avg'=>$lavg,
                'Access'=>$decodear[$accessRight],'Base'=>$base,'Scenario'=>$lgName,
        ]);
        $avg=$avg+$runTime;
    }
    
    foreach ($stypes as $stype) {
        $aggrNum[$stype]=0;
        $aggrRunTime[$stype]=0;
    }
    for ($i = 0; $i < $numberRunTot; $i++) {
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

//$log->show();

if ($saveLog) {
    $timestamp = time();
    $logname = '/PerfRun/'.$timestamp;
    echo "\nLogName: ".$logname."\n";
    $log->save('C:/Users/pierr/ABridge/Datastore/', $logname);
    saveLog($timestamp);
}

function saveLog($LogName)
{
    Log::reset();
    Mod::reset();
    Hdl::reset();
    Usr::reset();
    Adm::reset();
    Vew::reset();
    
    $path = "App/LOG/SETUP.php";
    require_once $path;
    $ctrl = new Controler(Config::$config, ['name'=>'LOG']);
    
    $_GET['Action']=CstMode::V_S_CREA;
    $_SERVER['PATH_INFO']='/PrfFile';
    $_SERVER['REQUEST_METHOD']='POST';
    $_POST['Name']=$LogName;
    $_POST['Load']='true';
    
    $hdl=$ctrl->run(false);
    $hdl->getErrLog()->show();
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
            'memBase'=>'test_perf',
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
    public $CName;
    protected $code;
    protected $cookieName;
    public $ckey;
    
    public function __construct($accessRight, $baseType)
    {
        $this->baseTypes=[$baseType];
        $classes = ['Student','Code','Role','Session','User'];
        $prm=UtilsC::genPrm($classes, get_called_class(), $this->baseTypes);
        
        $this->config['Handlers']=$prm['handlers'];
        
        if ($accessRight != 0) {
            $usr=[
                    'Role' => $prm[$baseType]['Role'],
                    'User' => $prm[$baseType]['User'],
                    'Session' => $prm[$baseType]['Session'],
            ];
            $this->config['Hdl']= ['Usr'=>$usr];
        }
        
        $this->cookieName='test_perf'.$prm[$baseType]['Session'];
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
    
    
    public function setKey()
    {
        $classes = ['Session'];
        $prm=UtilsC::genPrm($classes, get_called_class(), $this->baseTypes);
        $basetype = $this->baseTypes[0];
        $prm['application']= $this->ini;
        Mod::get()->init($prm['application'], $prm['handlers']);
        Mod::get()->begin();
        $mod = new Model('Controler_Perf_'.$basetype.'_Session', 1);
        $obj = $mod->getCobj();
        $this->ckey=$obj->getKey();
        Mod::get()->end();
    }
    
    
    
    public function initMod($accessRight, $size)
    {
        $classes = ['Student','Code',];
        $prm=UtilsC::genPrm($classes, get_called_class(), $this->baseTypes);
        $prm['application']= $this->ini;
        
        $basetype = $this->baseTypes[0];

        
        Mod::get()->init($prm['application'], $prm['handlers']);
        
        $this->CName=$prm[$basetype]['Student'];
        $this->code=$prm[$basetype]['Code'];

        
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
                [[CstMode::V_S_CREA,CstMode::V_S_UPDT],  [$homep.'|Cref|Cref|Cref'],          [$home=>':User','Cref'=>':User']],
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
            
        $res = ModUtils::initModBindings($prm[$basetype]);
            
        $role = $prm[$basetype]['Role'];
        $x = new Model($role);
        $x->setVal('Name', 'Default');
        $x->setVal('JSpec', $role1);
        if ($accessRight==2) {
            $x->setVal('JSpec', $role2);
        }
        $res=$x->save();
        echo $x->getErrLog()->show();
            
        $user = $prm[$basetype]['User'];
        $x = new Model($user);
        $x->setVal('UserId', 'test');
        $res=$x->save();
           
        $session = $prm[$basetype]['Session'];
        $x = new Model($session);
        $x->setVal('UserId', 'test');
        $x->setVal('RoleName', 'Default');
        $res=$x->save();
            
        $res=$x->save();
    
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
        $resc = $ctrl->run($this->show);
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
