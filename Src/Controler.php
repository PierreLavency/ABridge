<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\View\Vew;
use ABridge\ABridge\View\View;


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
    protected $isInit= [];
     
  
    public function __construct($spec, $ini)
    {
        
        $this->defVal=$this->defaultValues($spec, $ini);
        
        $this->spec=$spec;
        $bases = [];
        
        $this->initConf($this->defVal, $spec);
        
        if (! isset($this->spec['Hdl'])) {
            $this->spec['Hdl']=[];
        }
        if (! isset($this->spec['Log'])) {
            Log::get()->init($this->defVal, []);
        }
        $this->bases = Mod::get()->getBaseClasses();
    }

    
    protected function initConf($prm, $spec)
    {
        if (isset($spec['Handlers'])) {
            $config=$spec['Handlers'];
            Mod::get()->init($prm, $config);
        }
        if (isset($spec['Apps'])) {
            $specv = $spec['Apps'];
            foreach ($specv as $name => $config) {
                $className = 'ABridge\ABridge\Apps\\'.$name;
                $spece=$className::init($prm, $config);
                $this->initConf($prm, $spece);
            }
        }
        if (isset($spec['View'])) {
            $specv = $spec['View'];
            Vew::get()->init($prm, $specv);
        }
        if (isset($spec['Adm'])) {
            $config=$spec['Adm'];
            Adm::get()->init($prm, $config);
            $this->isInit['Adm']=true;
        }
        if (isset($spec['Hdl'])) {
            $config=$spec['Hdl'];
            Hdl::get()->init($prm, $config);
        }
        if (isset($spec['Log'])) {
            $config=$spec['Log'];
            Log::get()->init($prm, $config);
        }
    }
    
    private function defaultValues($spec, $ini)
    {
        $appName = $ini['name'];
        $this->appName = $appName;
        // priority : init - spec[Default] - default
        $defaultValues=[
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'base'=>'dataBase',
                'dataBase'=>$appName,
                'memBase'=>$appName,
                'fileBase'=>$appName,
                'host'=>'localhost',
                'user'=>$appName,
                'pass'=>$appName,
                'trace'=>0,
        ];
        if (isset($spec['Default'])) {
            $defaultValues=array_merge($defaultValues, $spec['Default']);
        }
        $defaultValues=array_merge($defaultValues, $ini);
        return $defaultValues;
    }
 

    protected function logUrl()
    {
        $log=Log::get();
        $urli = 'Uri : '.$this->handle->getDocRoot();
        $log->logLine(
            $urli,
            [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__]
        );
        $urlp = 'Path : '.$this->handle->getRpath();
        $log->logLine(
            $urlp,
            [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__]
        );
        $method = 'Method: '.$this->handle->getMethod();
        $log->logLine(
            $method,
            [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__]
        );
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
        return Mod::get()->begin();
    }
    
    public function end()
    {
        return Mod::get()->end();
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
    
        $this->begin();
        
        $frccommit=false;
        
        if (isset($this->isInit['Adm'])) {
            $adm=Adm::get()->begin();
            $frccommit=Adm::get()->isNew();
        }
       
        
        $this->handle= Hdl::get()->begin();
        $frccommit=($frccommit || Hdl::get()->isNew());

        $this->logUrl();
        
        $method=$this->handle->getMethod();
        
        if ($this->handle->getDocRoot() == '/ABridgeAPI.php') {
            GenJASON::genJASON($this->handle, true, true);
            $this->showLog();
            return $this->handle;
        }
        
        if ($this->handle->nullobj()) {
            Vew::get()->begin([$show, $this->handle]);
            if ($frccommit) {
                $this->end();
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
                $res=$this->end();
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
            $this->end();
        }
        
        Vew::get()->begin([$show, $this->handle]);

        Log::get()->end();
        
        return $this->handle;
    }
}
