
<?php

// must clean up interface with set up !!

require_once 'View.php';
require_once 'Request.php';
require_once 'SessionHdl.php';
require_once 'Handle.php';
require_once 'SessionMgr.php';
require_once 'GenJASON.php';


class Controler
{
    protected $bases=[];

    protected $handle=null;
    protected $request= null;
    protected $sessionHdl= null;
    protected $spec = [];
    protected $attrL = [];
    protected $valL = [];
    protected $opL = [];
    protected $logLevel = 0;
    protected $sessionMgr ;
    protected $bname ;
     
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
        $sesMgr = new SessionMgr();
        $this->sessionMgr=$sesMgr;
        foreach ($config as $classN => $handler) {
            if (count($handler) == 1) {
                    $handler[]=$this->bname;
            }
            if (! in_array($handler, $handlers)) {
                $handlers[] = $handler;
                if ($handler[0]== 'fileSession') {
                    $id=$sesMgr->initSession($handler);
                    $x= Handler::get()->getBaseNm(
                        $handler[0],
                        $handler[1],
                        $id
                    );
                } else {
                    $x = Handler::get()->getBase(
                        $handler[0],
                        $handler[1]
                    );
                }
                $bases[]=$x;
            }
            initStateHandler($classN, $handler[0], $handler[1]);
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
        $bname = $ini['name'];
        if (isset($init['bname'])) {
            $bname = $init['bname'];
        }
        $this->bname = $bname;
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
        if ($level == 1) {
            $urli = $this->request->getDocRoot();
            echo 'uri is '.$urli. '<br>';
            $urlp = $this->request->getRpath();
            echo 'path is '.$urlp. '<br>';
            $method = $this->request->getMethod();
            echo 'method is '.$method. '<br>';
        }
        foreach ($this->bases as $base) {
            $base-> setLogLevl($level);
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
    
    protected function close()
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
            $cond = false;
            if ($action == V_S_SLCT) {
                $cond = $c->isSelect($attr);
            } else {
                $cond = $c->isModif($attr);
            }
            $typ= $c->getTyp($attr);
            $val=$this->request->getPrm($attr);
            if (!is_null($val)) {
                $valC = convertString($val, $typ);
                if ($c->isModif($attr)) {
                    $c->setVal($attr, $valC);
                }
                if (!is_null($valC) and $c->isSelect($attr)) {
                    $this->attrL[]=$attr;
                    $this->valL[]=$valC;
                }
            }
            $name=$attr.'_OP';
            $val=$this->request->getPrm($name);
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
        $home=$spec['Home'];
        $v->setTopMenu($home);
        $action = $this->request->getAction();
        $v->show($action, $show);
        return true;
    }
    
    public function run($show, $logLevel)
    {
        $this->beginTrans();
        $this->sessionMgr->startSessions();
        if ($this->sessionMgr->isNew()) {
            $this->commit();
            $this->beginTrans();
        }
        $this->setLogLevl($logLevel);
        $this->sessionHdl= $this->sessionMgr->getHandle();
        $this->handle = new Handle($this->sessionHdl);
        $this->request = $this->handle->getReq();		
        $method=$this->request->getMethod();
        
        if ($this->request->getDocRoot() == '/ABridgeAPI.php') {
            genJASON($this->handle, true, true);
            $this->close();
            $this->showLog();
            return $this->handle->getPath();
        }
        
        if ($this->handle->nullobj()) {
            $this->showView($show);
            $this->close();
            $this->showLog();
            return $this->handle->getPath();
        }
        
        $action = $this->request->getAction();
        $actionExec = false;
        if ($method =='POST') {
            if ($action == V_S_UPDT
            or  $action == V_S_CREA
            or  $action==V_S_SLCT) {
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
            $rdoc =$this->request->getDocRoot();
            if ($action == V_S_DELT) {
                $npath = $this->request->popObj();
                $this->handle = new Handle($npath,V_S_READ, $this->sessionHdl);
            }

            if ($action != V_S_SLCT) {
                $this->request->setAction(V_S_READ);
            }
        }
        $this->showView($show);
        $this->close();
        $this->showLog();
        return $this->handle->getPath();
    }
}
