<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Logger;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\Handler;
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
        
        Handler::get()->resetHandlers();
        Mod::get()->reset();
        Hdl::get()->reset();
        Usr::get()->reset();
        Adm::get()->reset();
        Vew::get()->reset();

        $this->initConf($spec);
        
        if (! isset($this->spec['Hdl'])) {
            $this->spec['Hdl']=[];
        }
        
        $this->bases = Handler::get()->getBaseClasses();
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
        
        if (isset($ini['path'])) {
            $this->defVal['path']= $ini['path'];
        }
        if (!isset($this->defVal['path'])) {
            $this->defVal['path']='C:/Users/pierr/ABridge/Datastore/';
        }

        if (isset($ini['base'])) {
            $this->defVal['base']=$ini['base'];
        }
        if (!isset($this->defVal['base'])) {
            $this->defVal['base']='dataBase';
        }
        
        if (isset($ini['dbnm'])) {
            $this->defVal['dbnm']=$ini['dbnm'];
        }
        if (!isset($this->defVal['dbnm'])) {
            $this->defVal['dbnm']=$appName;
        }
        
        if (isset($ini['menm'])) {
            $this->defVal['menm']=$ini['menm'];
        }
        if (!isset($this->defVal['menm'])) {
            $this->defVal['menm']=$appName;
        }
        if (isset($ini['flnm'])) {
            $this->defVal['flnm']=$ini['flnm'];
        }
        if (!isset($this->defVal['flnm'])) {
            $this->defVal['flnm']=$appName;
        }
        
        if (isset($ini['host'])) {
            $this->defVal['host']= $ini['host'];
        }
        if (!isset($this->defVal['host'])) {
            $this->defVal['host']='localhost';
        }
        
        if (isset($ini['user'])) {
            $this->defVal['user']=$ini['user'];
        }
        if (!isset($this->defVal['user'])) {
            $this->defVal['user']=$appName;
        }
                
        if (isset($ini['pass'])) {
            $this->defVal['pass']= $ini['pass'];
        }
        if (!isset($this->defVal['pass'])) {
            $this->defVal['pass']=$this->defVal['user'];
        }
        
        Logger::setPath($this->defVal['path']);
    }
 
    
    
    public function beginTrans()
    {
        Mod::get()->begin(null, null);
    }
    
    protected function setLogLevl($level)
    {
        $this->logLevel=$level;
        if (!$level) {
            return true;
        }

        foreach ($this->bases as $base) {
            $base-> setLogLevl($level);
        }
    }

    protected function logUrl()
    {
        if ($this->logLevel > 0) {
            $urli = $this->handle->getDocRoot();
            echo 'uri is '.$urli. '<br>';
            $urlp = $this->handle->getRpath();
            echo 'path is '.$urlp. '<br>';
            $method = $this->handle->getMethod();
            echo 'method is '.$method. '<br>';
        }
    }

    protected function logStartView()
    {
        if ($this->logLevel <=1) {
            return true;
        }
        foreach ($this->bases as $base) {
            $log = $base-> getLog();
            $log->logLine(' **************  ');
        }
    }
    
    protected function showLog()
    {
        if ($this->logLevel<=1) {
            return true;
        }
        foreach ($this->bases as $base) {
            $log = $base-> getLog();
            $log->show();
        }
    }
    
    public function commit()
    {
        return  Mod::get()->end();
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
        Mod::get()->begin(null, null);
        
        $frccommit=false;
              
        if (isset($this->spec['Adm'])) {
            $adm=Adm::get()->begin($this->defVal, $this->spec['Adm']);
            $frccommit=Adm::get()->isNew();
        }
 
        if (isset($this->spec['Hdl'])) {
            $this->handle= Hdl::get()->begin($this->defVal, $this->spec['Hdl']);
            $frccommit=($frccommit || Hdl::get()->isNew());
        }
        $this->setLogLevl($logLevel);
        $this->logUrl();
        $method=$this->handle->getMethod();
        
        if ($this->handle->getDocRoot() == '/ABridgeAPI.php') {
            GenJASON::genJASON($this->handle, true, true);
            $this->showLog();
            return $this->handle;
        }
        
        if ($this->handle->nullobj()) {
            $this->logStartView();
            Vew::get()->begin($show, $this->handle);
            $this->showLog();
            if ($frccommit) {
                $this->commit();
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
                $res=$this->commit();
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
            $this->commit();
        }
        
        $this->logStartView();
        Vew::get()->begin($show, $this->handle);

        $this->showLog();
        return $this->handle;
    }
}
