
<?php

Class Path
{
    protected $_pathStrg;
    protected $_pathArr;
    protected $_pathNrmArr;
    protected $_pathCreat;
    protected $_pathPrefix='/ABridge.php';
    protected $_home='/'; 
     protected $_homeObj=null;
    protected $_objN;
    protected $_isHome;
    protected $_length;
    protected $_obj=null;
    
    protected $_objA= [];
    protected $_pathA=[];


  
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
            $this->construct1($_SERVER['PATH_INFO']);
            return;
        }
        $this->construct1($this->_home);
    }
 
    protected function construct1($pathStrg) 
    {
        $this->_objA= [];
        $this->_pathA= [];
        $this->initHome();  
        $this->initPath($pathStrg);
        $this->initObj();   
    }

    
    public function setHome($path) 
    {
        $this->_home = $path; 
        $this->initHome();
        $this->initObj();
        return true;        
    }

    private function initHome() 
    {
        $home = $this->_home; 
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
    
    private function homeIsRoot() 
    {
        return is_null($this->_homeObj);
    }

    private function homeObj() 
    {
        return $this->_homeObj;
    }
    
    public  function getHomePath() 
    {
        $path=$this->prfxPath('/');
        return $path;
    }

    public function homeMod() 
    {
        return $this->_homeObj->getModName();
    }

    private function homeId() 
    {
        return $this->_homeObj->getId();
    }   
    
    protected function initPath($pathStrg)
    {
        $pathArr = explode('/', $pathStrg);
        $this->_pathStrg=$pathStrg;
        if ($pathArr[0] != "") { // not starting with /  ?
            throw new Exception(E_ERC036.':'.$pathStrg);
        }
        array_shift($pathArr);
        if ($pathArr[0] == "") { // '/' alones ?
            $this->_isHome=true;
            return;
        }
        $this->_isHome=false;
        $c = count($pathArr);
        $r = $c%2;
        $obj = null;
        $this->_pathArr=[];
        $this->_length=$c;
        $this->_pathCreat = $r;
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
        if ($this->_pathCreat) {
            if (! ctype_alnum($pathArr[$c-1])) {
                $j = $c-1;
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$j);
            }
            $this->_pathArr[$c-1]=$pathArr[$c-1];
        }
    }
 
    private function arrToPath ($parr)
    {
        $path = '/'.implode('/', $parr);
        return $path;
    }

    public function prfxPath($path)
    {
        return $this->_pathPrefix.$path;
    }

    public function getPath() 
    {
        $path = $this->prfxPath($this->_pathStrg);
        return $path;
    }
        
    public function isCreatPath() 
    {
        if ($this->_isHome) {
            return false;
        }
        if ($this->_pathCreat) {
            return true;
        }
        return false; 
    }   
 
    public function isObjPath() 
    {
        if ($this->_isHome) {
            return false;
        }
        if ($this->_pathCreat) {
            return false;
        }
        return true; 
    }   

    public function pushId($id) 
    {
        if ($this->_isHome) {
            throw new Exception(E_ERC038);
        }
        if ($this->isCreatPath()) {
            $path = $this->_pathStrg.'/'.$id;           
            $this->_pathA[]=$this->_pathStrg;
            $this->_objA[]=$this->getObj();     
            $this->initPath($path);
            $this->_obj=new Model($this->getObj()->getModName(), (int) $id);
            return true;
        }
        throw new Exception(E_ERC037);
    }
    
    public function popId() 
    {
        if ($this->_isHome) {
            throw new Exception(E_ERC038);
        }
        if ($this->isCreatPath()) {
            throw new Exception(E_ERC035);
        }
        $res = $this->_pathArr;
        array_pop($res);
        $path = $this->arrToPath($res);
        $opath = array_pop($this->_pathA);
        $this->_obj=array_pop($this->_objA);        
        $this->initPath($opath);
        return true;
    }
    
    public function push($attr,$id) 
    {
        if ($this->isCreatPath()) {
            throw new Exception(E_ERC035);
        }
        $path = $this->_pathStrg.'/'.$attr.'/'.$id;
        if ($this->_isHome) {
            $path = '/'.$attr.'/'.$id;
        }
        $this->_pathA[]=$this->_pathStrg;
        $this->_objA[]=$this->getObj();
        $this->initPath($path);
        $this->_obj=$this->_obj->getCref($attr, (int) $id);
        return true; 
    }
    
    public function pop()
    {
        if ($this->_isHome) {
            return true;
        }
        if ($this->isCreatPath()) {
            throw new Exception(E_ERC035);
        }
        if ($this->_length <= 2 ) {
            $this->initPath('/');
            $this->_obj=null;
            return true;
        }
        $res = $this->_pathArr;
        array_pop($res);
        array_pop($res);
        $path = $this->arrToPath($res);
        $opath = array_pop($this->_pathA);
        $this->_obj=array_pop($this->_objA);
        $this->initPath($opath);
        return true;
    }

    public function popDel()
    {
        if ($this->_isHome) {
            return true;
        }
        if ($this->isCreatPath()) {
            throw new Exception(E_ERC035);
        }
        if ($this->_length <= 2 ) {
            $this->initPath('/');
            $this->_obj=null;
            return true;
        }
        $res = $this->_pathArr;
        array_pop($res);
        array_pop($res);
        $path = $this->arrToPath($res);
        $this->construct1($path);
        return true;
    }


    
    public function getAction() 
    {
        $method = $_SERVER['REQUEST_METHOD'];  
        $action = V_S_READ;
        if ($method == 'GET') {
            if (isset($_GET['View'])) {
                $action = $_GET['View'];
                return $action; 
            }
            if ($this->isCreatPath()) {
                $action = V_S_CREA;
            }
        }
        if ($method =='POST') {
            $action = $_POST['action'];
        }
        return $action; 
    }
        
    public function getActionPath($action) 
    {
        if ($this->_isHome) {
            return null;
        }
        if ($action == V_S_UPDT or $action == V_S_DELT) {
            if ($this->isCreatPath()) {
                throw new Exception(E_ERC037.':'.$action);
            }
            if (!$this->linkToHome($this->getObj())) {
                return null;
            }
            $path = $this->getPath();
            $path = "'".$path.'?View='.$action."'";
            return $path;
        }
        if ($action == V_B_CANC) {
            $path = $this->getObjPath();
            return $path;
        }
        $path = $this->getCreaPath();
        if (! $this->mlinkToHome($this->getObj())) {
            return null;
        }
        if ($action ==V_S_CREA) {
            return $path;
        }
        if ($action==V_S_SLCT or $action ==V_B_RFCH) {
             $path = "'".$path.'?View='.V_S_SLCT."'";
             return $path;
        }       
    }
    
    public function getObjPath() 
    {
        if ($this->_isHome) {
            throw new Exception(E_ERC038);
        }       
        if (! $this->isCreatPath()) {
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
    
    public function getCreaPath() 
    {
        if ($this->_isHome) {
            throw new Exception(E_ERC038);
        }
        if ($this->isCreatPath()) {
            return $this->prfxPath($this->_pathStrg);
        }
        $res = $this->_pathArr;
        array_pop($res);
        $path = $this->arrToPath($res); 
        $path = $this->prfxPath($path);
        return $path;
    }
    
    public function getClassPath($mod,$action) 
    {
        
        $x = new Model($mod);
        $res = $this->mlinkToHome($x);
        if (!$res) {
            return null;
        }
        $path="'".$this->prfxPath('/'.$mod).'?View='.$action."'";
        return $path;
    }
   
    public function getCrefPath($attr,$action)
    {
        // $action = V_S_CREA   
        $obj = $this->getObj();
        if (! $this->linkToHome($this->getObj())) {
            return null;
        }   
        $path = $this->getPath();
        $path= "'".$path.'/'.$attr."'";
        return $path;
    }
     
    public function getObj() 
    {
        return $this->_obj;
    }

    public function getCref($attr, $id) 
    {
        if ($this->isCreatPath()) {
            throw new Exception(E_ERC035);
        }
        $path = $this->_pathStrg.'/'.$attr.'/'.$id;
        if ($this->_isHome) {
            $path = '/'.$attr.'/'.$id;
        }
        $p= new Path($path, $this->_homeObj);
        return $p;
    }
 
    public function initObj()
    {
        
        $this->_obj=null;
        $obj = null;
        $fobj = null;
 
        if ($this->_isHome) {
            $this->_obj = $this->homeObj();
            return $this->homeObj();
        }

        $this->pathNrmArr=[];
        for ($i=0; $i<$this->_objN; $i=$i+2) {
            $mod = $this->_pathArr[$i];
            $id  = $this->_pathArr[$i+1];
            if (is_null($obj)) {
                $obj = new Model($mod, $id);
                $fobj = $obj;
            } else {
                $obj = $obj->getCref($mod, $id);
            }
            $this->_pathNrmArr[]=$obj->getModName();
            $this->_pathNrmArr[]=$obj->getId();
        }
        if ($this->isCreatPath()) {
            $c = $this->_length;
            $mod =  $this->_pathArr[$c-1];
            if ($c == 1) {
                $obj = new Model($mod);
            } else {
                $obj = $obj->newCref($mod);
            }
            $this->_pathNrmArr[]=$obj->getModName();
        }

        if ($this->homeIsRoot()) {
            $this->_obj =$obj;
            return $obj;
        }

        if (! $this->linkToHome($fobj) and $this->isObjPath()) {
            return null;
        }
        
        $attr = $this->homeMod();

        if ($obj->existsAttr($attr)) {
            if ($obj->isProtected($attr)) {
                $this->_obj =$obj;
                return $obj;
            }
            if ($this->isCreatPath()) {
                $obj->setVal($attr, $this->homeId());
            }
            $obj->protect($attr);
        }
        $this->_obj =$obj;
        return $obj;
    }

    private function linkToHome($obj) 
    {
        if ($this->homeisRoot()) {
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

    private function mlinkToHome($obj) 
    {
        if ($this->homeisRoot()) {
            return true;
        }
        if (is_null($obj)) {
            return false;
        }
        $attr = $this->homeMod();
        if ($obj->existsAttr($attr)) {
            return true;
        }
        return false; 
    }

    
    public function getRefPath($obj)
    {
        if (is_null($obj)) {
            return null;
        }
        $mod= $obj->getModName();
        $id = $obj->getId();

        $path=$this->prfxPath('/'.$mod.'/'.$id);    
        if ($this->_isHome) {
            return $path;
        } 
        if (is_null($this->_pathNrmArr)) {
            $this->getObj(); // should be error
        }       
        $c = $this->_length;
        $resN = $this->_pathNrmArr;
        $res  = $this->_pathArr;
        $found = false;
        while ((count($res) > 1) and (! $found)) {
            $rid  = array_pop($resN);
            $rmod = array_pop($resN);
            if ($mod == $rmod and $rid == $id) {
                $found = true;
            } else {
                array_pop($res);
                array_pop($res);
            }
        }
        if ($found) {
            $path = $this->arrToPath($res);
            $path=$this->prfxPath($path);
            return $path;
        }

        if ($this->homeIsRoot()) {
            return $path;
        }

        $hmod = $this->homeMod();
        $hid = $this->homeId();
        
        if ($obj->existsAttr($hmod) and $obj->getTyp($hmod) == M_REF) {
            $oid = $obj->getVal($hmod);
            $omod =  $obj->getRefMod($hmod);
            if ($oid==$hid and $hmod == $omod) {
                $path = '/'.$mod.'/'.$id;
                $path=$this->prfxPath($path);
                return $path;
            }
        }   
        
        return null; 

    }
}
