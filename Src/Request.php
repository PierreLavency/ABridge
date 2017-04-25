
<?php
require_once 'CstError.php';
require_once 'CstMode.php';

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
        if (isset($_SERVER['PHP_SELF'])) {
            $uri = explode('/', $_SERVER['PHP_SELF']);
            if (count($uri) > 1) {
                self::$docRoot='/'.$uri[1];
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
            throw new Exception(E_ERC036.':'.$pathStrg);
        }
        array_shift($pathArr);
        if ($pathArr[0] == "") { // '/' alones
            $this->isRoot=true;
            $this->modPath='|';
            return;
        }
        $this->modPath="";
        $this->isRoot=false;
        $c = count($pathArr);
        $r = $c%2;
        $obj = null;
        $this->pathArr=[];
        $this->length=$c;
        $this->isClassPath = $r;
        $this->objN = $c-$r;
        for ($i=0; $i < $this->objN; $i=$i+2) {
            if (! checkIdentifier($pathArr[$i])) {
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$i);
            }
            if (! ctype_digit($pathArr[$i+1])) {
                $j=$i+1;
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$j);
            }
            $this->pathArr[]=$pathArr[$i];
            $this->modPath=$this->modPath.'|'.$pathArr[$i];
            $this->pathArr[]=(int) $pathArr[$i+1];
        }
        if ($this->isClassPath) {
            if (! checkIdentifier($pathArr[$c-1])) {
                $j = $c-1;
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$j);
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
    
    public function getUrl()
    {
        $url= self::$docRoot.$this->path;
        $action =$this->getAction();
        if ($action != V_S_READ) {
            $url=$url.'?Action='.$action;
        }
        return $url;
    }

    public function getDocRoot()
    {
        return self::$docRoot;
    }
    
    public function getRootUrl()
    {
        $path = $this->getDocRoot().'/';
        return $path;
    }

    public function isRoot()
    {
        return ($this->isRoot);
    }
// deprecated use Url.
    public function getPath()
    {
        $path = $this->getDocRoot().$this->path;
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
        $this->construct2($path, V_S_READ);
        return $path;
    }

    public function pushId($id)
    {
        if (! $this->isClassPath()) {
            throw new Exception(E_ERC037);
        }
        $path = $this->path.'/'.$id;
        $this->construct2($path, V_S_READ);
        return $path;
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
    
    protected function checkActionPath($action)
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
        if ($this->isRoot()) {
            if ($action == V_S_READ) {
                return true;
            }
        }
        throw new Exception(E_ERC048.':'.$action.':'.$this->getPath());
    }

    public function getActionReq($action)
    {
        if ($this->isRoot()) {
            if ($action == V_S_READ) {
                return new request($this->getRPath(), $action);
            }
            return null;
        }
        if ($this->isObjPath()) {
            if ($action == V_S_READ) {
                return new request($this->getRPath(), $action);
            }
            if ($action == V_S_UPDT or $action == V_S_DELT) {
                return new request($this->getRPath(), $action);
            }
            if ($action == V_S_SLCT or $action == V_S_CREA) {
                $res = $this->pathArr;
                array_pop($res);
                $path = $this->arrToPath($res);
                return new request($path, $action);
            }
        }
        if ($this->isClassPath()) {
            if ($action == V_S_READ) {
                $res = $this->pathArr;
                array_pop($res);
                if (count($res)==0) {
                    $path= '/';
                } else {
                    $path = $this->arrToPath($res);
                }
                return new request($path, $action);
            }
            if ($action == V_S_SLCT or $action == V_S_CREA) {
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
