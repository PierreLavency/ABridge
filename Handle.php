
<?php

require_once('Request.php');
require_once('Home.php');
require_once('Model.php');

Class Handle
{

    protected $_home=null; 
    protected $_request=null; 
    protected $_obj=null;
    
    protected $_mainObj=null;
    protected $_pathNrmArr;
    protected $_startHome=false;

    public function __construct() 
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = 'construct'.$i)) {
        call_user_func_array(array($this, $f), $a);
        }
    }

    protected function construct2($request,$home) 
    {
        $this->_request = $request; 
        $this->_home = $home;
        $this->initObj();
        $action=$this->_request->getAction();
        $res = $this->checkActionObj($action);
        if (!$res) {
            throw new Exception(E_ERC049.':'.$action);
        }
    }

    protected function construct4($req,$home,$obj,$main) 
    {
        $this->_request = $req; 
        $this->_home = $home;
        $this->_obj = $obj;
        $this->_mainObj = $main;
    }
    
    private function initObj()
    {
        $this->_obj=null;
        $this->_startHome = false;
        $obj = null;
        $fobj = null;

        if ($this->_request->isHomePath()) {
            $this->_obj = $this->_home->getObj();
            $this->_startHome = true;
            return $this->_obj;
        }
        $this->pathNrmArr=[];
        $pathArr= $this->_request->pathArr();
        $objN   = $this->_request->objN();
        for ($i=0; $i<$objN; $i=$i+2) {
            $mod = $pathArr[$i];
            $id  = $pathArr[$i+1];
            if (is_null($obj)) {
                $obj = new Model($mod, $id);
                if ($this->_home->isLinked($obj)) {
                    $this->_startHome = true;
                }
            } else {
                $obj = $obj->getCref($mod, $id);
            }
            $this->_pathNrmArr[]=$obj->getModName();
            $this->_pathNrmArr[]=$obj->getId();
        }
        if ($this->_request->isClassPath()) {
            $c = count($pathArr);
            $mod =  $pathArr[$c-1];
            if ($c == 1) {
                $obj = new Model($mod);
                if ($this->_home->canLink($obj)) {
                    $this->_startHome = true;
                }               
            } else {
                $obj = $obj->newCref($mod);
            }
            $this->_pathNrmArr[]=$obj->getModName();
        }
        $this->_home->hlink($obj);
        $this->_obj =$obj;
    }

    public function nullObj() 
    {
        return (is_null($this->_obj));
    }
    
    protected function isMain()
    {
        return (is_null($this->_mainObj));
    }

    protected function getMain()
    {
        $res = $this->_mainObj;
        if (is_null($res)) {
            return $this;
        } else {
            return $res->getMain();
        }
    }
    
    protected function checkActionObj($action)
    {
        if (! $this->isMain()) {
            return false;
        }       
        if ($this->_home->isRoot()) {
            return true;
        }
        if ($this->_startHome) {
            switch ($action) {
                case V_S_READ :
                    return true;
                case V_S_CREA :
                case V_S_SLCT :
                    if (count($this->_request->pathArr()) == 1) {
                    return $this->_home->canLink($this->_obj);
                    }
                    return true;
                case V_S_UPDT :
                case V_S_DELT :
                    return ($this->_home->isLinked($this->_obj));
            }
        }
        return false;
    }
    
    public function isMainRef($attr)
    {
        $rid =  $this->getVal($attr);
        $rmod = $this->getModRef($attr);
        $main = $this->getMain();
        $cid =  $main->getId();
        $cmod = $main->getModName();
        if ($cid == $rid and $cmod == $rmod and $rid!= 0) {
            return true;
        }
        $cmod = $main->getAbstrNme();
        if ($cid == $rid and $cmod == $rmod and $rid!= 0) {
            return true;
        }
        return false;
    }   
    
// from req
    public function getPath()
    {
        if (is_null($this->_request)) {
            return null;
        }
        return $this->_request->getPath();
    }

    public function getRPath()
    {
        if (is_null($this->_request)) {
            return null;
        }
        return $this->_request->getRPath();
    }
 
    public function getAction()
    {
        if (is_null($this->_request)) {
            return null;
        }
        return $this->_request->getAction();
    }
    
    public function setAction($action)
    {
        if (is_null($this->_request)) {
            return null;
        }
        return $this->_request->setAction($action);
    }
        
// handle
 
    public function getObjId($id) 
    {
        $obj = new Model($this->_obj->getModName(), (int) $id);
        $path = $this->getRPath().'/'.$id;
        $req = new Request($path, V_S_READ); 
        $h= new Handle($req, $this->_home, $obj, $this);
        return $h; 
    }
  
    public function getCref($attr, $id) 
    {
        $obj = $this->_obj->getCref($attr, (int) $id);
        $path = $this->getRPath().'/'.$attr.'/'.$id;
        if ($this->_request->isHomePath()) {
            $path = '/'.$attr.'/'.$id;
        }
        $req = new Request($path, V_S_READ); 
        $h= new Handle($req, $this->_home, $obj, $this);
        return $h; 
    }

    public function getCode($attr,$id)
    {
        $obj = $this->_obj->getCode($attr, $id);
        $path = $this->getRefPath($obj);
        if (is_null($path)) {
            $req=null;
        } else {
            $req= new Request($path, V_S_READ);
        }       
        $h= new Handle($req, $this->_home, $obj, $this);
        return $h; 
    }
    
    public function getRef($attr)
    {
        $obj = $this->_obj->getRef($attr);
        if (is_null($obj)) {
            return null;
        }
        $path = $this->getRefPath($obj);
        if (is_null($path)) {
            $req=null;
        } else {
            $req= new Request($path, V_S_READ);
        }       
        $h= new Handle($req, $this->_home, $obj, $this);
        return $h; 
    }
        
    protected function getRefPath($obj)
    {
        if (!is_null($this->_mainObj)) {
            return ($this->_mainObj->getRefPath($obj));
        }
        
        $mod= $obj->getModName();
        $id = $obj->getId();
        $path='/'.$mod.'/'.$id;     
        $resN = $this->_pathNrmArr;
        $res  = $this->_request->pathArr();
        $c = count($res);
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
            $path = '/'.implode('/', $res);
            return $path;
        }
        if ($this->_home->isRoot()) {
            return $path;
        }         
        if ($this->_home->isLinked($obj)) {
            return $path;
        }
        return null; 
    }   
    

    
// get Path
    
    public function getActionPath($action) 
    {

        if (! $this->checkActionObj($action)) {
            return null;
        }
        if ($this->_home->isRoot() and $this->_request->isHomePath()) {
            return null;
        }
        return ($this->_request->getActionPath($action));   
    }
    
    public function getClassPath($mod,$action) 
    {    
        if (! $this->isMain()) {
            return null;
        }
        $x = new Model($mod);
        if ($this->_home->canLink($x)) {
            return $this->_request->getClassPath($mod, $action);
        }
        return null;
    }
   
    public function getCrefPath($attr,$action)
    {
        // $action = V_S_CREA
        if (! $this->isMain()) {
            return null;
        }
        if ($this->_request->getAction() != V_S_READ) {
            return null;
        }
        if (! $this->checkActionObj(V_S_UPDT)) {
            return null;
        }
        return $this->_request->getCrefPath($attr, $action);
    }
     
// obj  : access should be controlled here 
    
    public function getAttrList() 
    {
        return $this->_obj->getAttrList();
    }

    public function getModName()
    {
        return $this->_obj->getModName();
    }

    public function getId()
    {
        return $this->_obj->getId();
    }
    
    public function getTyp($attr)
    {
        return $this->_obj->getTyp($attr);
    }
    
    public function getVal($attr)
    {
        return $this->_obj->getVal($attr);
    }
    
    public function getDflt($attr)
    {
        return $this->_obj->getDflt($attr);
    }
    
    public function getValues($attr)
    {
        return $this->_obj->getValues($attr);
    }   
 
    public function getModCref($attr)
    {
        return $this->_obj->getModCref($attr);
    }   
   
    public function getAbstrNme()
    {
        return $this->_obj->getAbstrNme();
    }   
   
    public function getModRef($attr)
    {
        return $this->_obj->getModRef($attr);
    }   
    
    public function isProtected($attr)
    {
        return $this->_obj->isProtected($attr);
    }
      
    public function isMdtr($attr)
    {
        return $this->_obj->isMdtr($attr);
    }

    public function isEval($attr)
    {
        return $this->_obj->isEval($attr);
    }
    
    public function isModif($attr)
    {
        return $this->_obj->isModif($attr);
    }

    public function isSelect($attr)
    {
        return $this->_obj->isSelect($attr);
    }

    public function setVal($attr,$val)
    {
        return $this->_obj->setVal($attr, $val);
    }

    public function save()
    {
        return $this->_obj->save();
    }

    public function setCriteria($attrL, $opL, $valL)
    {
        return $this->_obj->setCriteria($attrL, $opL, $valL);
    }   

    public function select()
    {
        return $this->_obj->select();
    }
    
    public function delet()
    {
        $res = $this->_obj->delet();
        return $res;
    }
    
    public function isErr()
    {
        return $this->_obj->isErr();
    }

    public function getErrLog()
    {
        return $this->_obj->getErrLog();
    }
}
