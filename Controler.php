
<?php

// must clean up interface with set up !!

require_once("Model.php"); 
require_once("View.php"); 
require_once("Path.php"); 

class Controler
{
    protected $_bases=[];
    protected $_obj = null;
    protected $_spec = [];
    protected $_attrL = [];
    protected $_valL = [];
    protected $_logLevel = 0;
    
    function __construct($spec) 
    {
        $this->_spec=$spec;
        $bases = [];
        $handlers = [];
        resetHandlers();
        $config=$spec['Handlers'];
        foreach ($config as $classN => $handler) {
            if (! in_array($handler, $handlers)) {
                $handlers[] = $handler;
                $x = getBaseHandler($handler[0], $handler[1]);
                $bases[]=$x;
            }
            initStateHandler($classN, $handler[0], $handler[1]);
        }
        $this->_bases = $bases;
    }

    protected function beginTrans() 
    {
        foreach ($this->_bases as $base) {
            $base-> beginTrans();
        }
    }
    
    protected function setLogLevl($level) 
    {
        $this->_logLevel=$level;
        if (!$level) {
            return true;
        }
        if ($level == 1) {
            if (isset($_SERVER['PATH_INFO'])) {
                $url=$_SERVER['PATH_INFO'];
                echo 'url is '.$url. '<br>';
            } else {
                echo 'url is null'. '<br>';
            }
            $method = $_SERVER['REQUEST_METHOD'];
            echo 'method is '.$method. '<br>';
        }
        foreach ($this->_bases as $base) {
            $base-> setLogLevl($level);
        }

    }
    
    protected function logStartView() 
    {
        if ($this->_logLevel <=1) {
            return true;
        }
        foreach ($this->_bases as $base) {
            $log = $base-> getLog();
            $log->logLine(' **************  ');
        }
    }
    
    protected function showLog() 
    {
        if ($this->_logLevel<=1) {
            return true;
        }
        foreach ($this->_bases as $base) {
            $log = $base-> getLog();
            $log->show();
        }
    }
    
    protected function commit()
    {
        $res = true;
        foreach ($this->_bases as $base) {
            $r =$base->commit();
            $res = ($res and $r);
        }
        return $res;
    }
    
    protected function close()
    {
        $res = true;
        foreach ($this->_bases as $base) {
            $r =$base->close();
            $res = ($res and $r);
        }
        return $res;
    }
    
    protected function rollback()
    {
        $res = true;
        foreach ($this->_bases as $base) {
            $r =$base->rollback();
            $res = ($res and $r);
        }
        return $res;
    }
    
    protected function setVal() 
    {
        $c= $this->_obj;
        foreach ($c->getAllAttr() as $attr) { 
            if ($c->isMdtr($attr) or $c->isOptl($attr)) {
                if (isset($_POST[$attr])) {
                    $val= $_POST[$attr];
                    $typ= $c->getTyp($attr);
                    $valC = convertString($val, $typ);
                    $c->setVal($attr, $valC);
                    if (!is_null($valC)) {
                        $this->_attrL[]=$attr;
                        $this->_valL[]=$valC;
                    }
                }
                if ($c->isProtected($attr)) {
                    $this->_attrL[]=$attr;
                    $this->_valL[]=$c->getVal($attr);
                }
            }
        }
        return (!$c->isErr());
    }
        
    protected function showView($path,$action,$show) 
    {
        $this->logStartView();
        $spec = $this->_spec;
        if (isset($spec['Views'])) {
            $specv = $spec['Views'];
            foreach ($specv as $mod=>$specm) {
                Handler::get()->setViewHandler($mod, $specm);
            }
        }       
        $v=new View($this->_obj);
        $home=$spec['Home'];
        $v->setNavClass($home);
        if (is_null($this->_obj)) {
            $v->show($path, $action, $show);
            return;
        }
        $v->show($path, $action, $show);
    }
    
    function run($show,$logLevel)
    {
        $method = $_SERVER['REQUEST_METHOD'];   
        $this->beginTrans();
        $this->setLogLevl($logLevel);   
        $path = new Path();
        $this->_obj  = $path->getObj();
        if (is_null($this->_obj)) {
            $this->showView($path, null, $show);
            $this->close();
            $this->showLog();
            return $path->getPath();
        }
        $action = $path->getAction();
        $actionExec = false;
        if ($method =='POST') {
            if ($action == V_S_UPDT 
            or $action == V_S_CREA 
            or $action==V_S_SLCT) {
                $res = $this->setVal();
            }
            if (!$this->_obj->isErr()) {
                if ($action == V_S_DELT) {
                    $this->_obj->delet();
                }
                if ($action == V_S_UPDT or $action == V_S_CREA) {
                    $this->_obj->save();         
                }
                if ($action == V_S_SLCT) {
                    $valL=$this->_valL;
                    $attrL=$this->_attrL;
                    $res = $this->_obj->setCriteria($attrL, $valL);
                }
            }
            if (!$this->_obj->isErr()) {
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
                $path->pop();
                $this->_obj= $path->getObj();
            }
            if ($action == V_S_CREA) {
                $path->pushId($this->_obj->getId());
            }
            if ($action != V_S_SLCT) {
                $action= V_S_READ;
            }
        }
        $this->showView($path, $action, $show);
        $this->close();
        $this->showLog();
        return $path->getPath();
    }
}


