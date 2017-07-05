
<?php

require_once '/View/Src/View.php';
require_once 'Handle.php';
require_once 'GenJASON.php';
require_once 'Find.php';

class Controler
{
    protected $bases=[];

    protected $handle=null;
    protected $sessionHdl= null;
    protected $spec = [];
    protected $attrL = [];
    protected $valL = [];
    protected $opL = [];
    protected $logLevel = 0;
    protected $sessionMgr = null;
    protected $appName ;
    protected $classList=[];
     
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
        $this->init($ini);
    }
 
    public function construct2($spec, $ini)
    {
        $this->init($ini);
        $this->spec=$spec;
        $bases = [];
        $handlers = [];
        
        resetHandlers();
        $config=$spec['Handlers'];
        foreach ($config as $classN => $handler) {
            $menu=true;
            $c = count($handler);
            if ($c==3) {
                $menu = array_pop($handler);
                $c=2;
            }
            switch ($c) {
                case 0:
                    break;
                case 1:
                    $handler[]=$this->appName;
                    // default
                case 2:
                    if (! in_array($handler, $handlers)) {
                        $handlers[] = $handler;
                        $x = Handler::get()->getBase($handler[0], $handler[1]);
                        $bases[]=$x;
                    }
                    Handler::get()->setStateHandler($classN, $handler[0], $handler[1]);
                    break;
            }
            if ($menu) {
                $this->classList[]=$classN;
            }
        }
        $this->bases = $bases;
    }
 
    private function init($ini)
    {
        $fpath= 'C:/Users/pierr/ABridge/Datastore/';
        if (isset($ini['path'])) {
            $fpath= $ini['path'];
        }
        Logger::setPath($fpath);
        Base::setPath($fpath);
        $host = 'localhost';
        if (isset($ini['host'])) {
            $host= $ini['host'];
        }
        $usr = 'cl822';
        if (isset($ini['user'])) {
            $usr= $ini['user'];
        }
        $psw = 'cl822';
        if (isset($ini['pass'])) {
            $psw= $ini['pass'];
        }
        SQLBase::setDB($host, $usr, $psw);
        $appName = $ini['name'];
        $this->appName = $appName;
    }
 
    public function beginTrans()
    {
        foreach ($this->bases as $base) {
            $base-> beginTrans();
        }
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
        $res = true;
        foreach ($this->bases as $base) {
            $r =$base->commit();
            $res = ($res and $r);
        }
        return $res;
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
            $val= $c->getPrm($attr, isRaw($typ));
            if (!is_null($val)) {
                $valC = convertString($val, $typ);
                if ($action != V_S_SLCT and $c->isModif($attr)) {
                    $c->setVal($attr, $valC);
                }
                if ($action != V_S_SLCT and  $attr=='vnum') {
                    $c->checkVers((int) $valC);
                }
                if ($action == V_S_SLCT and !is_null($valC) and $c->isSelect($attr)) {
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
        
    protected function showView($show)
    {
        $this->logStartView();
        $spec = $this->spec;
        if (isset($spec['Views'])) {
            $specv = $spec['Views'];
            foreach ($specv as $mod => $specm) {
                Handler::get()->setViewHandler($mod, $specm);
            }
        }
        $v=new View($this->handle);
        $home = [];
        if (isset($spec['Home'])) {
            $home=$spec['Home'];
        }
        if ($this->sessionHdl) {
            $selmenu = $this->sessionHdl->getSelMenu($this->classList);
        } else {
            $selmenu = [];
            foreach ($this->classList as $classElm) {
                    $selmenu[]='/'.$classElm;
            }
        }
        $menu = array_unique(array_merge($home, $selmenu));
        $v->setTopMenu($menu);
        $action = $this->handle->getAction();
        $v->show($action, $show);
        return true;
    }
    
    public function run($show, $logLevel)
    {
        $this->beginTrans();
        
        if (isset($this->spec['Adm'])) {
            require_once 'Adm/Src/Adm.php';
            
            Adm::init($this->appName, $this->spec['Adm']);
            if (Adm::isNew()) {
                $this->commit();
                $this->beginTrans();
            }
        }
        
        if (isset($this->spec['Usr'])) {
            require_once 'Usr/Src/Usr.php';
            
            $sessionHdl= Usr::init($this->appName, ['Session']);
            $this->sessionHdl= $sessionHdl;
            if (Usr::isNew()) {
                $this->commit();
                $this->beginTrans();
                $this->handle = new Handle('/Session/~', V_S_UPDT, $this->sessionHdl);
            } else {
                $this->handle = new Handle($this->sessionHdl);
            }
        } else {
            $this->handle = new Handle(null);
        }


        $this->setLogLevl($logLevel);
        $this->logUrl();
        $method=$this->handle->getMethod();
        
        if ($this->handle->getDocRoot() == '/ABridgeAPI.php') {
            genJASON($this->handle, true, true);
            $this->showLog();
            return $this->handle;
        }
        if ($this->handle->nullobj()) {
            $this->showView($show);
            $this->showLog();
            return $this->handle;
        }
        $action = $this->handle->getAction();
        $actionExec = false;
        if ($method =='POST') {
            if ($action == V_S_UPDT
            or  $action == V_S_CREA
            or  $action == V_S_SLCT) {
                $res = $this->setVal($action);
            }
            if (!$this->handle->isErr()) {
                if ($action == V_S_DELT) {
                    $this->handle->delet();
                }
                if ($action == V_S_UPDT or $action == V_S_CREA) {
                    $this->handle->save();
                }
                if ($action == V_S_SLCT) {
                    $valL=$this->valL;
                    $attrL=$this->attrL;
                    $opL=$this->opL;
                    $res = $this->handle->setCriteria($attrL, $opL, $valL);
                }
            }
            if (!$this->handle->isErr()) {
                $res=$this->commit();
                if ($res) {
                    $actionExec=true;
                }
            } else {
                $this->rollback();
            }
        }
        if ($actionExec) {
            if ($action == V_S_DELT) {
                $this->handle=$this->handle->getDD();
            }
            if ($action != V_S_SLCT) {
                $this->handle->setAction(V_S_READ);
            }
        }
        $this->showView($show);
        $this->showLog();
        return $this->handle;
    }
}
