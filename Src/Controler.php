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



use ABridge\ABridge\GenJASON;
use ABridge\ABridge\View\CstView;

class Controler
{

    protected $handle=null;
    protected $spec = [];
    protected $attrL = [];
    protected $valL = [];
    protected $oprL = [];
    protected $ordL = [];
    protected $logLevel = 0;
    protected $appName ;
    protected $defVal=[];
     
  
    public function __construct($config)
    {
        Log::get()->init($config->getPrm(), []);
        $config->init();
    }
    
    protected function logUrl()
    {
        $log=Log::get();
        $urli = 'Uri : '.$this->handle->getDocRoot();
        $lineInfo = [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $log->logLine($urli, $lineInfo);
        $urlp = 'Path : '.$this->handle->getRpath();
        $lineInfo = [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $log->logLine($urlp, $lineInfo);
        $method = 'Method: '.$this->handle->getMethod();
        $lineInfo = [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $log->logLine($method, $lineInfo);
    }

    
    public function close()
    {
        return Mod::get()->close();
    }
    
    public function rollback()
    {
        return Mod::get()->rollback();
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
                $this->oprL[$attr]=$val;
            }
            if ($c->isProtected($attr)) {
                $this->attrL[]=$attr;
                $this->valL[]=$c->getVal($attr);
            }
        }
        $val = $c->getPrm(CstView::V_P_SRT, false);
        if ($val) {
            $desc = $c->getPrm(CstView::V_P_DSC, false);
            if (is_null($desc)) {
                $desc=false;
            }
            $this->ordL= [[$val,$desc]];
        }
        return (!$c->isErr());
    }
 
    public function run($show)
    {
        Log::get()->begin();
    
        $this->begin();
        
        $frccommit=false;
        
        if (Adm::get()->isInit()) {
            Adm::get()->begin();
            $frccommit=($frccommit ||Adm::get()->isNew());
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
                    $oprL=$this->oprL;
                    $ordL=$this->ordL;
                    $res = $this->handle->setCriteria($attrL, $oprL, $valL, $ordL);
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
