
<?php

Class Path
{
    protected $_pathStrg;
    protected $_pathArr;
    protected $_pathNrmArr;
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
    
    public function isCreatPath() 
    {
        if ($this->_pathCreat) {
            return true;
        }
        return false; 
    }
    
    public function getDefaultPath() 
    {
        return $this->_default; 
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
    
    public function rootPath()
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
    
    public function getObjPath() 
    {
        if (! $this->isCreatPath()) {
            return $this->rootPath().$this->_pathStrg;
        }
        $res = $this->_pathArr;
        array_pop($res);
        if (count($res)==0) {
            return $this->rootPath().$this->getDefaultPath();
        }
        $path = $this->rootPath().'/'.implode('/', $res);
        return $path;
    }
    
    public function getCreaPath() 
    {
        if ($this->isCreatPath()) {
            return $this->rootPath().$this->_pathStrg;
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
