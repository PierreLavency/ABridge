
<?php

require_once('ErrorConstant.php');

Class Request
{
    protected $_pathPrefix='/ABridge.php';
    protected $_path=null;
    protected $_pathArr;
    protected $_objN;
    protected $_length; 
    protected $_isClassPath=false;
    protected $_isObjPath=false;
    protected $_isHomePath=false;   
    protected $_action=null;

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
        if (isset($_SERVER['PATH_INFO'])) { 
            $path = $_SERVER['PATH_INFO'];
        }
        $this->initPath($path);
        $this->initAction();
        $this->checkActionPath($this->getAction());
    }
 
    protected function construct2($path,$action) 
    {
        $this->initPath($path);
        $this->_action=$action;
        $this->checkActionPath($this->getAction());     
    }
    
// Path
     
    protected function initPath($pathStrg)
    {
        $pathArr = explode('/', $pathStrg);
        $this->_path=$pathStrg;
        if ($pathArr[0] != "") { // not starting with /  ?
            throw new Exception(E_ERC036.':'.$pathStrg);
        }
        array_shift($pathArr);
        if ($pathArr[0] == "") { // '/' alones 
            $this->_isHomePath=true;
            return;
        }
        $this->_isHomePath=false;
        $c = count($pathArr);
        $r = $c%2;
        $obj = null;
        $this->_pathArr=[];
        $this->_length=$c;
        $this->_isClassPath = $r;
        $this->_objN = $c-$r;
        for ($i=0; $i < $this->_objN; $i=$i+2) {
            if (! ctype_alnum($pathArr[$i])) {
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$i);
            }
            if (! ctype_digit($pathArr[$i+1])) {
                $j=$i+1;
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$j);
            }
            $this->_pathArr[]=$pathArr[$i];;
            $this->_pathArr[]=(int) $pathArr[$i+1];;
        }
        if ($this->_isClassPath) {
            if (! ctype_alnum($pathArr[$c-1])) {
                $j = $c-1;
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$j);
            }
            $this->_pathArr[$c-1]=$pathArr[$c-1];
        }
    }

    public function prfxPath($path)
    {
        return $this->_pathPrefix.$path;
    }

    public function getPath() 
    {
        $path = $this->prfxPath($this->_path);
        return $path;
    }

    public function getRPath()
    {
        return $this->_path;
    }
    
    public function pathArr() 
    {
        return $this->_pathArr;
    }


    public function objN() 
    {
        return $this->_objN;
    }
    
    public function isHomePath() 
    {
        return ($this->_isHomePath);
    }   
    
    public function isClassPath() 
    {
        if ($this->_isHomePath) {
            return false;
        }
        if ($this->_isClassPath) {
            return true;
        }
        return false; 
    }   
 
    public function isObjPath() 
    {
        if ($this->_isHomePath) {
            return false;
        }
        if ($this->_isClassPath) {
            return false;
        }
        return true; 
    }   
 
    protected function arrToPath ($parr)
    {
        $path = '/'.implode('/', $parr);
        return $path;
    }
 
    protected function objPath() 
    {
        if ($this->isHomePath()) {
            throw new Exception(E_ERC038);
        }   
        if ($this->isObjPath()) {
            $path = $this->getPath();
            return $path;
        }
        $res = $this->_pathArr;
        array_pop($res);
        if (count($res)==0) {
            return $this->prfxPath('/');
        }
        $path = $this->arrToPath($res); 
        $path = $this->prfxPath($path);     
        return $path;
    }
    
    protected function classPath() 
    {
        if ($this->isHomePath()) {
            throw new Exception(E_ERC038);
        }
        if ($this->isClassPath()) {
            return $this->getPath();
        }
        $res = $this->_pathArr;
        array_pop($res);
        $path = $this->arrToPath($res); 
        $path = $this->prfxPath($path);
        return $path;
    }
        
    public function pop()
    {
        if ($this->isClassPath()) {
            throw new Exception(E_ERC035);
        }
        if ($this->_length <= 2 ) {
            $path ='/';
            return $path;
        }
        $res = $this->_pathArr;
        array_pop($res);
        array_pop($res);
        $path = $this->arrToPath($res);
        return $path; 
    }

// Action   
    
    protected function initAction() 
    {
        $method = $_SERVER['REQUEST_METHOD'];  
        if ($method == 'GET') {
            $this->_action = V_S_READ;
            if (isset($_GET['View'])) {
                $this->_action = $_GET['View'];
                return $this->_action; 
            }
            if ($this->isClassPath()) {
                $this->_action = V_S_SLCT;
                return $this->_action; 
            }
            return $this->_action;
        }
        if ($method =='POST') {
            if (isset($_POST['action'])) {
                $this->_action = $_POST['action'];
                return $this->_action; 
            }
            if ($this->isClassPath()) {
                $this->_action = V_S_CREA;
                return $this->_action; 
            }
        }
        throw new Exception(E_ERC048);
    }
    
    public function getAction() 
    {
        return $this->_action;
    }

    public function checkActionPath($action) 
    {
        if ($this->isClassPath()) {
            if ($action == V_S_SLCT or $action == V_S_CREA) {
                return true;
            }
        }
        if ($this->isObjPath()) {
            if ($action == V_S_READ or 
            $action == V_S_UPDT or 
            $action ==V_S_DELT  ) {
                return true;
            }
        }
        if ($this->isHomePath()) {
            if ($action == V_S_READ or $action == V_S_UPDT ) {
                return true;
            }
        }
        throw new Exception(E_ERC048.':'.$action);
    }
    
    public function getActionPath($action) 
    {
        if ($this->isHomePath()) {
            if ($action == V_S_READ) {
                return $this->getPath();
            }
            if ($action == V_S_UPDT) {
                $path = $this->getPath().'?View='.$action;
                return $path;
            }
        }
        if ($this->isObjPath()) {
            if ($action == V_S_READ) {
                return $this->getPath();
            }
            if ($action == V_S_UPDT or $action == V_S_DELT) {
                $path = $this->getPath().'?View='.$action;
                return $path;
            }
            if ($action == V_S_SLCT or $action == V_S_CREA) {
                $path = $this->classPath().'?View='.$action;
                return $path;
            }
        }
        if ($this->isClassPath()) {
            if ($action == V_S_READ) { 
                return $this->objPath();
            }
            if ($action == V_S_SLCT or $action == V_S_CREA) {
                $path = $this->getPath().'?View='.$action;
                return $path;
            }
        }
        return null;    
    }
    
    public function getClassPath($mod,$action) 
    {    
        $path=$this->prfxPath('/'.$mod).'?View='.$action;
        return $path;
    }
   
    public function getCrefPath($attr,$action)
    {
        $path = $this->getPath();
        $path= $path.'/'.$attr.'?View='.$action;
        return $path;
    }

}
