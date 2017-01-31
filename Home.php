
<?php

require_once('ErrorConstant.php');
require_once('Model.php');

Class Home
{
    protected $_homeObj=null;
    
    public function __construct() 
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = 'construct'.$i)) {
        call_user_func_array(array($this, $f), $a);
        }
    }
 
    protected function construct0() 
    {
        $this->initHome('/');
    }
 
    protected function construct1($path) 
    {
        $this->initHome($path);  
    }

    private function initHome($home) 
    {
        if ($home == '/') {
            $this->_homeObj = null;
            return true;
        }
        $apath = explode('/', $home);
        $mod = $apath[1];
        $id = (int) $apath[2];
        $this->_homeObj = new Model($mod, $id);
        return true;
    } 
    
    public function isRoot() 
    {
        return (is_null($this->_homeObj));
    }

    public function getObj() 
    {
        return $this->_homeObj;
    }
    
    protected function homeMod() 
    {
        $hobj = $this->_homeObj;
        return $hobj->getModName();
    }

    protected function homeId() 
    {
        $hobj = $this->_homeObj;
        return $hobj->getId();
    }   
 
    public function canLink($obj) 
    {
        if ($this->isRoot()) {
            return true;
        }
        if (is_null($obj)) {
            return false;
        }
        $attr = $this->homeMod();
        return ($obj->existsAttr($attr)); 
    }
 
    public function isLinked($obj) 
    {
        if ($this->isRoot()) {
            return true;
        }
        if (is_null($obj)) {
            return false;
        }
        $attr = $this->homeMod();
        if ($obj->existsAttr($attr)) {
            $id = $obj->getVal($attr);
            if ($id == $this->homeId()) {
                return true;
            }
        }
        return false; 
    }

    public function hlink($obj)
    {   
        if ($this->isRoot()) {
            return true;
        }
        if (is_null($obj)) {
            return false;
        }   
        $attr = $this->homeMod();
        if ($obj->existsAttr($attr)) {
            $id = $obj->getVal($attr);
            if ($id == $this->homeId()) {
                return true;
            }
            if ($id==0) {               
                $obj->setVal($attr, $this->homeId());
                $obj->protect($attr);
                return true;
            }
        }
        return false;
    }
        
}
