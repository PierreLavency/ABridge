
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
    protected $_opL = [];
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
    
    protected function setVal($action) 
    {
        $c= $this->_obj;
        foreach ($c->getAllAttr() as $attr) {
            $cond = false;
            if ($action == V_S_SLCT) {
                $cond = $c->isSelect($attr);
            } else {
                $cond = $c->isModif($attr);
            }
            $typ= $c->getTyp($attr);
            if (isset($_POST[$attr])) {
                $val= $_POST[$attr];
                $valC = convertString($val, $typ);
                if ($c->isModif($attr)) {
                    $c->setVal($attr, $valC);
                }
                if (!is_null($valC) and $c->isSelect($attr)) {
                    $this->_attrL[]=$attr;
                    $this->_valL[]=$valC;
                }
            }
            $name=$attr.'_OP';
            if (isset($_POST[$name])) {
                $this->_opL[$attr]=$_POST[$name];
            }
            if ($c->isProtected($attr)) {
                $this->_attrL[]=$attr;
                $this->_valL[]=$c->getVal($attr);
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
        $v->show($path, $action, $show);
        return true;
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
                $res = $this->setVal($action);
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
                    $opL=$this->_opL;
                    $res = $this->_obj->setCriteria($attrL, $opL, $valL);
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


