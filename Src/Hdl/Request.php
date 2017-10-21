<?php
namespace ABridge\ABridge\Hdl;

use Exception;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\CstMode;

class Request
{
    // CPath = docRoot + path + action
 
    protected static $docRoot='/ABridge.php';
    protected $path=null;
    protected $modPath = null;
    protected $pathArr;
    protected $objN;
    protected $length;
    protected $isClassPath=false;
    protected $isObjPath=false;
    protected $isRoot=false;
    protected $isT=false;
    protected $action=null;
    protected $method=null;
    protected $getp=null;
    protected $postp=null;

    protected $role = [];
    protected $cond = [];
    
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
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            if (count($uri) > 1) {
                self::$docRoot='/'.$uri[1];
            }
        }
        $path='/';
        if (isset($_SERVER['PATH_INFO'])) {
            $path = trim($_SERVER['PATH_INFO']);
        }
        $this->getp   = $this->cleanInputs($_GET);
        $this->postp  = $this->cleanInputs($_POST);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->initPath($path);
        $this->initAction();
        $this->checkActionPath($this->getAction());
    }
 
    protected function construct1($path)
    {
        $this->initPath($path);
        $this->method='GET';
        $this->initAction();
        $this->checkActionPath($this->getAction());
    }

    protected function construct2($path, $action)
    {
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
            throw new Exception(CstError::E_ERC036.':'.$pathStrg);
        }
        array_shift($pathArr);
        if ($pathArr[0] == "") { // '/' alones
            $this->isRoot=true;
            $this->modPath='|';
            return;
        }
        $this->isRoot=false;
        $this->modPath="";
        $c = count($pathArr);
        $r = $c%2;
        $this->pathArr=[];
        $this->length=$c;
        $this->isClassPath = $r;
        $this->objN = $c-$r;
        for ($i=0; $i < $this->objN; $i=$i+2) {
            if (! Mtype::checkIdentifier($pathArr[$i])) {
                throw new Exception(CstError::E_ERC036.':'.$pathStrg.':'.$i);
            }
            $this->pathArr[]=$pathArr[$i];
            $this->modPath=$this->modPath.'|'.$pathArr[$i];
            if ($i==0 and $pathArr[$i+1] ==  '~') {
                $this->isT=true;
                $this->pathArr[]=$pathArr[$i+1];
            } else {
                if (! ctype_digit($pathArr[$i+1])) {
                    $j=$i+1;
                    throw new Exception(CstError::E_ERC036.':'.$pathStrg.':'.$j);
                }
                $this->pathArr[]=(int) $pathArr[$i+1];
            }
        }
        if ($this->isClassPath) {
            if (! Mtype::checkIdentifier($pathArr[$c-1])) {
                $j = $c-1;
                throw new Exception(CstError::E_ERC036.':'.$pathStrg.':'.$j);
            }
            $this->pathArr[$c-1]=$pathArr[$c-1];
            $this->modPath=$this->modPath.'|'.$pathArr[$c-1];
        }
    }

    public function getMethod()
    {
        return $this->method;
    }
    
    public function getModPath()
    {
        $path = $this->modPath;
        return $path;
    }
    
    public function getUrl($prm = [])
    {
        $url= self::$docRoot.$this->path;
        $action =$this->getAction();
        $first = true;
        if ($action != CstMode::V_S_READ) {
            $url=$url.'?Action='.$action;
            $first = false;
        }
        foreach ($prm as $name => $value) {
            if ($first) {
                $url = $url.'?'.$name.'='.$value;
                $first = false;
            } else {
                $url = $url. ' & '.$name.'='.$value;
            }
        }
        return '"'.$url.'"';
    }

    public function getDocRoot()
    {
        return self::$docRoot;
    }
    
    public function getRootUrl()
    {
        $path = '"'.$this->getDocRoot().'/'.'"';
        return $path;
    }

    public function isRoot()
    {
        return ($this->isRoot);
    }

    public function isT()
    {
        return ($this->isT);
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
       
    public function isClassPath()
    {
        if ($this->isRoot) {
            return false;
        }
        if ($this->isClassPath) {
            return true;
        }
        return false;
    }
 
    public function isObjPath()
    {
        if ($this->isRoot) {
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
        
    public function popReq()
    {
        if ($this->isRoot) {
            return $this;
        }
        $res = $this->pathArr;
        if ($this->isClassPath()) {
            array_pop($res);
        }
        if ($this->isObjPath()) {
            array_pop($res);
            array_pop($res);
        }
        $path = $this->arrToPath($res);
        return new Request($path, CstMode::V_S_READ);
    }

    public function pushId($id)
    {
        if (! $this->isClassPath()) {
            throw new Exception(CstError::E_ERC037);
        }
        $path = $this->path.'/'.$id;
        $this->construct2($path, CstMode::V_S_READ);
        return $path;
    }
    
// Action   

    protected function initAction()
    {
        $action = $this->getPrm('Action');
        if (! is_null($action)) {
            $this->action =$action;
            return $this->action;
        }
        if ($this->method == 'GET') {
            $this->action = CstMode::V_S_READ;
            if ($this->isClassPath()) {
                $this->action = CstMode::V_S_SLCT;
                return $this->action;
            }
            return $this->action;
        }
        if ($this->method =='POST') {
            if ($this->isClassPath()) {
                $this->action = CstMode::V_S_CREA;
                return $this->action;
            }
        }
        throw new Exception(CstError::E_ERC048);
    }
    
    protected function cleanInputs($data)
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
    
    public function getPrm($attr, $raw = false)
    {
        if ($raw) {
            return $this->getRawPrm($attr);
        }
        if (isset($this->getp[$attr])) {
            return $this->getp[$attr];
        }
        if (isset($this->postp[$attr])) {
            return $this->postp[$attr];
        }
        return null;
    }
 
    protected function getRawPrm($attr)
    {
        if (isset($_GET[$attr])) {
            return $_GET[$attr];
        }
        if (isset($_POST[$attr])) {
            return $_POST[$attr];
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
    
    protected function checkActionPath($action)
    {
        if ($this->isClassPath()) {
            if ($action == CstMode::V_S_SLCT or $action == CstMode::V_S_CREA) {
                return true;
            }
        }
        if ($this->isObjPath()) {
            if ($action == CstMode::V_S_READ or
            $action == CstMode::V_S_UPDT or
            $action ==CstMode::V_S_DELT  ) {
                return true;
            }
        }
        if ($this->isRoot()) {
            if ($action == CstMode::V_S_READ) {
                return true;
            }
        }
        throw new Exception(CstError::E_ERC048.':'.$action.':'.$this->getRPath());
    }

    public function getActionReq($action)
    {
        if ($this->isRoot()) {
            if ($action == CstMode::V_S_READ) {
                return new request($this->getRPath(), $action);
            }
            return null;
        }
        if ($this->isObjPath()) {
            if ($action == CstMode::V_S_READ) {
                return new request($this->getRPath(), $action);
            }
            if ($action == CstMode::V_S_UPDT or $action == CstMode::V_S_DELT) {
                return new request($this->getRPath(), $action);
            }
            if ($action == CstMode::V_S_SLCT or $action == CstMode::V_S_CREA) {
                $res = $this->pathArr;
                array_pop($res);
                $path = $this->arrToPath($res);
                return new request($path, $action);
            }
        }
        if ($this->isClassPath()) {
            if ($action == CstMode::V_S_READ) {
                $res = $this->pathArr;
                array_pop($res);
                $path= '/';
                if (count($res)>0) {
                    $path = $this->arrToPath($res);
                }
                return new request($path, $action);
            }
            if ($action == CstMode::V_S_SLCT or $action == CstMode::V_S_CREA) {
                return new request($this->getRPath(), $action);
            }
        }
        return null;
    }

    public function getObjReq($id, $action)
    {
        // for object selection in list
        $req = new Request($this->getRPath().'/'.$id, $action);
        return $req;
    }
       
    public function getCrefReq($attr, $action, $id = 0)
    {
        // for selection/creqtion of Cref
        $path=$this->getRPath().'/'.$attr;
        if ($id) {
            $path = $path.'/'.$id;
        }
        $req = new Request($path, $action);
        return $req;
    }
}
