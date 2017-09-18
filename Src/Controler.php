<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\Adm\Adm;

use ABridge\ABridge\View\Vew;
use ABridge\ABridge\View\View;

use ABridge\ABridge\Usr\Usr;

use ABridge\ABridge\GenJASON;

class Controler
{
    protected $bases=[];

    protected $handle=null;
    protected $spec = [];
    protected $attrL = [];
    protected $valL = [];
    protected $opL = [];
    protected $logLevel = 0;
    protected $appName ;
    protected $defVal=[];
     
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = 'construct'.$i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }
 
    public function construct1($ini)
    {
        $this->initPrm([], $ini);
    }
 
    public function construct2($spec, $ini)
    {
//    	$spec=\Config::$config;
        
        $this->initPrm($spec, $ini);
        
        $this->spec=$spec;
        $bases = [];
      
        Log::reset();
        Mod::reset();
        Hdl::reset();
        Usr::reset();
        Adm::reset();
        Vew::reset();

        $this->initConf($spec);
        
        if (! isset($this->spec['Hdl'])) {
            $this->spec['Hdl']=[];
        }
        if (! isset($this->spec['Log'])) {
            Log::get()->init($this->defVal, []);
        }
        $this->bases = Mod::get()->getBaseClasses();
    }
 
    protected function initConf($spec)
    {
        if (isset($spec['Handlers'])) {
            $config=$spec['Handlers'];
            Mod::get()->init($this->defVal, $config);
        }
        if (isset($spec['Apps'])) {
            $specv = $spec['Apps'];
            foreach ($specv as $name) {
                $className = 'ABridge\ABridge\Apps\\'.$name;
                $spece=$className::$config;
                $this->initConf($spece);
            }
        }
        if (isset($spec['View'])) {
            $specv = $spec['View'];
            Vew::get()->init($this->defVal, $specv);
        }
        if (isset($spec['Adm'])) {
            $config=$spec['Adm'];
            Adm::get()->init($this->defVal, $config);
            $this->spec['Adm']=$config;
        }
        if (isset($spec['Hdl'])) {
            $config=$spec['Hdl'];
            Hdl::get()->init($this->defVal, $config);
            $this->spec['Hdl']=$config;
        }
        if (isset($spec['Log'])) {
            $config=$spec['Log'];
            Log::get()->init($this->defVal, $config);
            $this->spec['Log']=$config;
        }
    }
    
    private function initPrm($spec, $ini)
    {
        // priority : init - spec[Default] - default
        
        if (isset($spec['Default'])) {
            $this->defVal=$spec['Default'];
        }
        
        $appName = $ini['name'];
        $this->appName = $appName;
        $this->defVal['name']=$appName;

        $paramList=['path','base','dataBase','memBase','fileBase','host','user','pass','trace'];
        foreach ($paramList as $param) {
        	if (isset($ini[$param])) {
        		$this->defVal[$param]= $ini[$param];
        	}
        }      
        if (!isset($this->defVal['path'])) {
            $this->defVal['path']='C:/Users/pierr/ABridge/Datastore/';
        }
        if (!isset($this->defVal['base'])) {
            $this->defVal['base']='dataBase';
        }        
        if (!isset($this->defVal['dataBase'])) {
            $this->defVal['dataBase']=$appName;
        }
        if (!isset($this->defVal['memBase'])) {
            $this->defVal['memBase']=$appName;
        }
        if (!isset($this->defVal['fileBase'])) {
            $this->defVal['fileBase']=$appName;
        }
        if (!isset($this->defVal['host'])) {
            $this->defVal['host']='localhost';
        }
        if (!isset($this->defVal['user'])) {
            $this->defVal['user']=$appName;
        }
        if (!isset($this->defVal['pass'])) {
            $this->defVal['pass']=$this->defVal['user'];
        }
        if (!isset($this->defVal['trace'])) {
            $this->defVal['trace']=0;
        }
    }
 

    protected function logUrl()
    {
        $log=Log::get();
        $urli = 'Uri : '.$this->handle->getDocRoot();
        $log->logLine($urli, ['class'=>__CLASS__]);
        $urlp = 'Path : '.$this->handle->getRpath();
        $log->logLine($urlp, ['class'=>__CLASS__]);
        $method = 'Method: '.$this->handle->getMethod();
        $log->logLine($method, ['class'=>__CLASS__]);
    }

    
    public function close()
    {
        $res = true;
        foreach ($this->bases as $base) {
            $r =$base->close();
            $res = ($res and $r);
        }
        return $res;
    }
    
    public function rollback()
    {
        $res = true;
        foreach ($this->bases as $base) {
            $r =$base->rollback();
            $res = ($res and $r);
        }
        return $res;
    }
    
    public function begin()
    {
        Mod::get()->begin();
    }
    
    public function end()
    {
        Mod::get()->end();
    }
    
    protected function setVal($action)
    {
        $c= $this->handle;
        foreach ($c->getAttrList() as $attr) {
            $typ= $c->getTyp($attr);
            $val= $c->getPrm($attr, Mtype::isRaw($typ));
            if (!is_null($val)) {
                $valC = Mtype::convertString($val, $typ);
                if ($action != CstMode::V_S_SLCT and $c->isModif($attr)) {
                    $c->setVal($attr, $valC);
                }
                if ($action != CstMode::V_S_SLCT and  $attr=='vnum') {
                    $c->checkVers((int) $valC);
                }
                if ($action == CstMode::V_S_SLCT and !is_null($valC) and $c->isSelect($attr)) {
                    $this->attrL[]=$attr;
                    $this->valL[]=$valC;
                }
            }
            $name=$attr.'_OP';
            $val=$c->getPrm($name);
            if (!is_null($val)) {
                $this->opL[$attr]=$val;
            }
            if ($c->isProtected($attr)) {
                $this->attrL[]=$attr;
                $this->valL[]=$c->getVal($attr);
            }
        }
        return (!$c->isErr());
    }
 
    public function run($show, $logLevel)
    {
        Log::get()->begin();
    
        Mod::get()->begin();
        
        $frccommit=false;
              
        if (isset($this->spec['Adm'])) {
            $adm=Adm::get()->begin($this->defVal, $this->spec['Adm']);
            $frccommit=Adm::get()->isNew();
        }
 
        if (isset($this->spec['Hdl'])) {
            $this->handle= Hdl::get()->begin($this->defVal, $this->spec['Hdl']);
            $frccommit=($frccommit || Hdl::get()->isNew());
        }
        $this->logUrl();
        
        $method=$this->handle->getMethod();
        
        if ($this->handle->getDocRoot() == '/ABridgeAPI.php') {
            GenJASON::genJASON($this->handle, true, true);
            $this->showLog();
            return $this->handle;
        }
        
        if ($this->handle->nullobj()) {
            Vew::get()->begin($show, $this->handle);
            if ($frccommit) {
                Mod::get()->end();
            }
            return $this->handle;
        }
        
        $action = $this->handle->getAction();
        $actionExec = false;
        if ($method =='POST') {
            if ($action == CstMode::V_S_UPDT
            or  $action == CstMode::V_S_CREA
            or  $action == CstMode::V_S_SLCT) {
                $res = $this->setVal($action);
            }
            if (!$this->handle->isErr()) {
                if ($action == CstMode::V_S_DELT) {
                    $this->handle->delet();
                }
                if ($action == CstMode::V_S_UPDT or $action == CstMode::V_S_CREA) {
                    $this->handle->save();
                }
                if ($action == CstMode::V_S_SLCT) {
                    $valL=$this->valL;
                    $attrL=$this->attrL;
                    $opL=$this->opL;
                    $res = $this->handle->setCriteria($attrL, $opL, $valL);
                }
            }
            if (!$this->handle->isErr()) {
                $res=Mod::get()->end();
                $frccommit=false;
                if ($res) {
                    $actionExec=true;
                }
            } else {
                $this->rollback();
            }
        }
        if ($actionExec) {
            if ($action == CstMode::V_S_DELT) {
                $this->handle=$this->handle->getDD();
            }
            if ($action != CstMode::V_S_SLCT) {
                $this->handle->setAction(CstMode::V_S_READ);
            }
        }
        
        if ($frccommit) {
            Mod::get()->end();
        }
        
        Vew::get()->begin($show, $this->handle);

        Log::get()->end();
        
        return $this->handle;
    }
}
