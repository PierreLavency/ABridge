
<?php

require_once 'CstError.php';
require_once 'CstMode.php';

class Request
{
    protected $docRoot='/ABridge.php';
    protected $path=null;
    protected $pathArr;
    protected $objN;
    protected $length;
    protected $isClassPath=false;
    protected $isObjPath=false;
    protected $isHomePath=false;
    protected $action=null;
    protected $method=null;
    protected $getp=null;
    protected $postp=null;
    
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
        if (isset($_SERVER['PHP_SELF'])) {
            $uri = explode('/', $_SERVER['PHP_SELF']);
            if (count($uri) > 1) {
                $this->docRoot='/'.$uri[1];
            }
        }
        if (isset($_SERVER['PATH_INFO'])) {
            $path = trim($_SERVER['PATH_INFO']);
        } else {
            $path='/';
        }
        $this->getp   = $this->cleanInputs($_GET);
        $this->postp  = $this->cleanInputs($_POST);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->initPath($path);
        $this->initAction();
        $this->checkActionPath($this->getAction());
    }
 
    protected function construct3($docRoot, $path, $action)
    {
        $this->docRoot=$docRoot;
        $this->initPath($path);
        $this->action=$action;
        $this->checkActionPath($this->getAction());
    }

// Path
     
    protected function initPath($pathStrg)
    {
        $pathArr = explode('/', $pathStrg);
        $this->path=$pathStrg;
        if ($pathArr[0] != "") { // not starting with /  ?
            throw new Exception(E_ERC036.':'.$pathStrg);
        }
        array_shift($pathArr);
        if ($pathArr[0] == "") { // '/' alones
            $this->isHomePath=true;
            return;
        }
        $this->isHomePath=false;
        $c = count($pathArr);
        $r = $c%2;
        $obj = null;
        $this->pathArr=[];
        $this->length=$c;
        $this->isClassPath = $r;
        $this->objN = $c-$r;
        for ($i=0; $i < $this->objN; $i=$i+2) {
            if (! ctype_alnum($pathArr[$i])) {
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$i);
            }
            if (! ctype_digit($pathArr[$i+1])) {
                $j=$i+1;
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$j);
            }
            $this->pathArr[]=$pathArr[$i];
            $this->pathArr[]=(int) $pathArr[$i+1];
        }
        if ($this->isClassPath) {
            if (! ctype_alnum($pathArr[$c-1])) {
                $j = $c-1;
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$j);
            }
            $this->pathArr[$c-1]=$pathArr[$c-1];
        }
    }

    public function getMethod()
    {
        return $this->method;
    }
    
    public function getDocRoot()
    {
        return $this->docRoot;
    }
    
    public function prfxPath($path)
    {
        
        return $this->docRoot.$path;
    }

    public function formPath($path, $action)
    {
        return $path.'?Action='.$action;
    }
    
    public function getHomePath()
    {
        $path = $this->prfxPath('/');
        return $path;
    }
    
    public function getPath()
    {
        $path = $this->prfxPath($this->path);
        return $path;
    }

    public function getRPath()
    {
        return $this->path;
    }
    
    public function pathArr()
    {
        return $this->pathArr;
    }


    public function objN()
    {
        return $this->objN;
    }
    
    public function isHomePath()
    {
        return ($this->isHomePath);
    }
    
    public function isClassPath()
    {
        if ($this->isHomePath) {
            return false;
        }
        if ($this->isClassPath) {
            return true;
        }
        return false;
    }
 
    public function isObjPath()
    {
        if ($this->isHomePath) {
            return false;
        }
        if ($this->isClassPath) {
            return false;
        }
        return true;
    }
 
    protected function arrToPath($parr)
    {
        $path = '/'.implode('/', $parr);
        return $path;
    }
 
    public function objPath()
    {
        if ($this->isHomePath()) {
            throw new Exception(E_ERC038);
        }
        if ($this->isObjPath()) {
            $path = $this->getPath();
            return $path;
        }
        $res = $this->pathArr;
        array_pop($res);
        if (count($res)==0) {
            return $this->prfxPath('/');
        }
        $path = $this->arrToPath($res);
        $path = $this->prfxPath($path);
        return $path;
    }
    
    public function classPath()
    {
        if ($this->isHomePath()) {
            throw new Exception(E_ERC038);
        }
        if ($this->isClassPath()) {
            return $this->getPath();
        }
        $res = $this->pathArr;
        array_pop($res);
        $path = $this->arrToPath($res);
        $path = $this->prfxPath($path);
        return $path;
    }
        
    public function popObj()
    {
        if ($this->isClassPath()) {
            throw new Exception(E_ERC035);
        }
        if ($this->length <= 2) {
            $path ='/';
            return $path;
        }
        $res = $this->pathArr;
        array_pop($res);
        array_pop($res);
        $path = $this->arrToPath($res);
        return $path;
    }

    public function pushId($id)
    {
        if (! $this->isClassPath()) {
            throw new Exception(E_ERC037);
        }
        return $this->path.'/'.$id;
    }
    
// Action   

    protected function initAction()
    {
        if ($this->method == 'GET') {
            $this->action = V_S_READ;
            if (isset($this->getp['Action'])) {
                $this->action =$this->getp['Action'];
                return $this->action;
            }
            if ($this->isClassPath()) {
                $this->action = V_S_SLCT;
                return $this->action;
            }
            return $this->action;
        }
        if ($this->method =='POST') {
            if (isset($this->postp['Action'])) {
                $this->action = $this->postp['Action'];
                return $this->action;
            }
            if ($this->isClassPath()) {
                $this->action = V_S_CREA;
                return $this->action;
            }
        }
        throw new Exception(E_ERC048);
    }
    
    private function cleanInputs($data)
    {
        $cleanInput = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $cleanInput[$k] = $this->cleanInputs($v);
            }
        } else {
            $cleanInput = trim(strip_tags($data));
        }
        return $cleanInput;
    }
    
    public function getPrm($attr)
    {
        if (isset($this->getp[$attr])) {
            return $this->getp[$attr];
        }
        if (isset($this->postp[$attr])) {
            return $this->postp[$attr];
        }
        return null;
    }
    
    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->checkActionPath($action);
        $this->action=$action;
        return true;
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
            if ($action == V_S_READ or $action == V_S_UPDT) {
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
                $path = $this->formPath($this->getPath(), $action);
                return $path;
            }
        }
        if ($this->isObjPath()) {
            if ($action == V_S_READ) {
                return $this->getPath();
            }
            if ($action == V_S_UPDT or $action == V_S_DELT) {
                $path = $this->formPath($this->getPath(), $action);
                return $path;
            }
            if ($action == V_S_SLCT or $action == V_S_CREA) {
                $path = $this->formPath($this->classPath(), $action);
                return $path;
            }
        }
        if ($this->isClassPath()) {
            if ($action == V_S_READ) {
                return $this->objPath();
            }
            if ($action == V_S_SLCT or $action == V_S_CREA) {
                $path = $this->formPath($this->getPath(), $action);
                return $path;
            }
        }
        return null;
    }
    
    public function getClassPath($mod, $action)
    {
        $path=$this->formPath($this->prfxPath('/'.$mod), $action);
        return $path;
    }
   
    public function getCrefPath($attr, $action)
    {
        $path = $this->getPath().'/'.$attr;
        $path= $this->formPath($path, $action);
        return $path;
    }
}
