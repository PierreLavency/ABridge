
<?php

Class Path
{
    protected $_pathStrg;
    protected $_parthArr;
    protected $_parthNrmArr;
    protected $_pathCreat;
    protected $_pathPrefix='Bridge.php';
    protected $_default='/Code/1';
    protected $_objN;
    protected $_length;
    
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
        $this->construct1($this->_default);
    }
    
    protected function construct1($pathStrg)
    {
        $pathArr = explode('/', $pathStrg);
        if ($pathArr[0] != "") { // not starting with / 
            throw new Exception(E_ERC036.':'.$pathStrg);
        }
        array_shift($pathArr);
        if ($pathArr[0] == "") { // '/' alones ?
            throw new Exception(E_ERC036.':'.$pathStrg);
        }
        $c = count($pathArr);
        $r = $c%2;
        $obj = null;
        $this->_pathStrg=$pathStrg;
        $this->_pathArr=[];
        $this->_length=$c;
        $this->_pathCreat = $r;
        $this->_objN = $c-$r;
        for ($i=0; $i < $this->_objN; $i=$i+2) {
            if (! ctype_alnum($pathArr[$i])) {
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$i);
            }
            $mod = $pathArr[$i];
            if (! ctype_digit($pathArr[$i+1])) {
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$i+1);
            }
            $this->_pathArr[]=$pathArr[$i];;
            $this->_pathArr[]=(int) $pathArr[$i+1];;
        }
        if ($this->_pathCreat) {
            if (! ctype_alnum($pathArr[$c-1])) {
                throw new Exception(E_ERC036.':'.$pathStrg.':'.$c-1);
            }
            $this->_pathArr[$c-1]=$pathArr[$c-1];
        }
    }
    
    public function isCreatPath() 
    {
        if ($this->_pathCreat) {
            return true;
        }
        return false; 
    }
    
    public function pushId($id) 
    {
        if ($this->isCreatPath()) {
            $path = $this->_pathStrg.'/'.$id;
            $this->construct1($path);
            return true;
        }
        throw new Exception(E_ERC037);
    }
    
    public function push($mod,$id) 
    {
        if ($this->isCreatPath()) {
            throw new Exception(E_ERC035);
        }
        $path = $this->_pathStrg.'/'.$mod.'/'.$id;
        $this->construct1($path);
        return true; 
    }
    
    public function pop()
    {
        if ($this->isCreatPath()) {
            throw new Exception(E_ERC035);
        }
        if ($this->_length <= 2) {
            $this->construct1($this->_default);
            return true;
        }
        $res = $this->_pathArr;
        array_pop($res);
        array_pop($res);
        $path = '/'.implode('/', $res);
        $this->construct1($path);
        return true;
    }
    
    protected function rootPath()
    {
        return ('/ABridge.php');
    }
    
    public function getObj()
    {
        $obj = null;
        $this->pathNrmArr=[];
        for ($i=0; $i<$this->_objN; $i=$i+2) {
            $mod = $this->_pathArr[$i];
            $id  = $this->_pathArr[$i+1];
            if ($i == 0) {
                $obj = new Model($mod, $id);
            } else {
                $obj = $obj->getCref($mod, $id);
            }
            $this->_pathNrmArr[]=$obj->getModName();
            $this->_pathNrmArr[]=$obj->getId();
        }
        if ($this->_pathCreat) {
            $c = $this->_length;
            $mod =  $this->_pathArr[$c-1];
            if ($c == 1) {
                $obj = new Model($mod);
            } else {
                $obj = $obj->newCref($mod);
            }
            $this->_pathNrmArr[]=$obj->getModName();
        }
        return $obj;
    }
    
    public function getPath() 
    {
        $path = $this->rootPath().$this->_pathStrg;
        return $path;
    }
    
    public function getCreaPath() 
    {
        if ($this->isCreatPath()) {
            return $this->_pathStrg;
        }
        $res = $this->_pathArr;
        array_pop($res);
        $path = $this->rootPath().'/'.implode('/', $res);
        return $path;
    }
    
    public function getRefPath($obj)
    {
        if (is_null($this->_pathNrmArr)) {
            $this->getObj();
        }       
        $mod = $obj->getModName();
        $id = $obj->getId();
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
            $path=$this->rootPath().'/'.implode('/', $res);
            return $path;
        }
        $path=$this->rootPath().'/'.$mod.'/'.$id;
        return $path;
    }

}

// should cut here 

function rootPath()
{
    return ('/ABridge.php');
}

function checkPath($apath) 
{
    $path=explode('/', $apath);
    $root = $path[0];
    if (($root != "" )) {
        return false;
    }
    if (count($path) < 2) {
        return false;
    }
    return true;
}

function getPath($model) 
{
    if (isset($_SERVER['PATH_INFO'])) { // to be checked
        $apath= explode('/', $_SERVER['PATH_INFO']);
        if (! $model->getId()) {
            return rootPath().$_SERVER['PATH_INFO'];
        }
        $id = (int) array_pop($apath);
        if ($id == $model->getId()) {
            $path = rootPath().$_SERVER['PATH_INFO'];
            return $path;
        }       
        $path = rootPath().$_SERVER['PATH_INFO'].'/'.$model->getId();
    } else {
        $path = refPath($model->getModName(), $model->getId()); 
    }
    return $path;
}

function getCreatePath($model) 
{
    if (isset($_SERVER['PATH_INFO'])) { // to be checked
        $apath= explode('/', $_SERVER['PATH_INFO']);
        $id= array_pop($apath);
        if ($id ==$model->getId()) {
            
            $path=implode('/', $apath);
            $path = rootPath().$path;
            return $path;
        }
        return rootPath().$_SERVER['PATH_INFO'];
    } else {
        $path = refPath($model->getModName(), 0); 
    }
    return $path;
}

function refPath($ref,$id) 
{
    if ($id) {
    $path=rootPath().'/'.$ref.'/'.$id;
    return $path;       
    }
    $path=rootPath().'/'.$ref;
    return $path;
}

function modPath($mod) 
{
    $path='/'.$mod;
    return $path;
}

function objAbsPath($model) 
{
    $rootPath= rootPath();
    $path = objPath($model);
    $path = $rootPath.$path;
    return $path;
}

function objPath ($model)
{
    $path = '/'.$model->getModName();
    if ($model->getId()) {
        $path=$path.'/'.$model->getId();
    }
    return $path;
}

function pathObj($path)
{
    $apath=explode('/', $path);
    return (apathObj($apath));
}

function apathObj($apath)
{
    $c = count($apath);
    if ($c > 5) {
        return false;
    }
    if ($c < 2 ) {
        return false;
    }
    if ($apath[0] != "" ) {
        return false;
    }
    if ($apath[1] == "" ) {
        return false;
    }
    if ($c == 5) {
        $id = (int) $apath[2];
        $mod = new Model($apath[1], $id);
        $mod = $mod->getCref($apath[3], (int) $apath[4]);
    } 
    if ($c == 4) {
        $id = (int) $apath[2];
        $mod = new Model($apath[1], $id);
        $mod = $mod->newCref($apath[3]);
    }
    if ($c == 3) {
        $id = (int) $apath[2];
        $mod = new Model($apath[1], $id);
    }
    if ($c == 2) {
        $mod = new Model($apath[1]);
    }
    return $mod;
}

function pathVal($path)
{
    $apath=explode('/', $path);
    if (count($apath) > 4) {
        return false;
    }
    if (count($apath) < 4 ) {
        return false;
    }
    $attr=array_pop($apath);
    $mod = apathObj($apath);
    if (!$mod) {
        return false;
    }
    $val = $mod->getVal($attr);
    return $val;
}

