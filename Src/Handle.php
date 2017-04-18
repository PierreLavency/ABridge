
<?php

require_once 'Request.php';
require_once 'Home.php';
require_once 'Model.php';

class Handle
{
    protected $home=null;
    protected $request=null;
    protected $obj=null;
    protected $mainObj=null;
    protected $pathNrmArr;
    protected $startHome=false;

    
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = 'construct'.$i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    protected function construct2($request, $home)
    {
        $this->request = $request;
        $this->home = $home;
        $this->initObj();
        $action=$this->request->getAction();
        $res = $this->checkActionObj($action);
        if (!$res) {
            throw new Exception(E_ERC049.':'.$action);
        }
    }

    protected function construct4($req, $home, $obj, $main)
    {
        $this->request = $req;
        $this->home = $home;
        $this->obj = $obj;
        $this->mainObj = $main;
    }
    
    private function initObj()
    {
        $this->obj=null;
        $this->startHome = false;
        $obj = null;
        $fobj = null;

        if ($this->request->isRootPath()) {
            $this->obj = $this->home->getObj();
            $this->startHome = true;
            return $this->obj;
        }
        $this->pathNrmArr=[];
        $pathArr= $this->request->pathArr();
        $objN   = $this->request->objN();
        for ($i=0; $i<$objN; $i=$i+2) {
            $mod = $pathArr[$i];
            $id  = $pathArr[$i+1];
            if (is_null($obj)) {
                $obj = new Model($mod, $id);
                if ($this->home->isLinked($obj)) {
                    $this->startHome = true;
                }
            } else {
                $obj = $obj->getCref($mod, $id);
            }
            $this->pathNrmArr[]=$obj->getModName();
            $this->pathNrmArr[]=$obj->getId();
        }
        if ($this->request->isClassPath()) {
            $c = count($pathArr);
            $mod =  $pathArr[$c-1];
            if ($c == 1) {
                $obj = new Model($mod);
                if ($this->home->canLink($obj)) {
                    $this->startHome = true;
                }
            } else {
                $obj = $obj->newCref($mod);
            }
            $this->pathNrmArr[]=$obj->getModName();
        }
        $this->home->hlink($obj);
        $this->obj =$obj;
    }
 
    protected function checkActionObj($action)
    {
        if (! $this->isMain()) {
            return false;
        }
        if ($this->home->isRoot()) {
            return true;
        }
        if ($this->startHome) {
            switch ($action) {
                case V_S_READ:
                    return true;
                case V_S_CREA:
                case V_S_SLCT:
                    if (count($this->request->pathArr()) == 1) {
                        return $this->home->canLink($this->obj);
                    }
                    return true;
                case V_S_UPDT:
                case V_S_DELT:
                    return ($this->home->isLinked($this->obj));
            }
        }
        return false;
    }

    public function nullObj()
    {
        return (is_null($this->obj));
    }
    
    protected function isMain()
    {
        return (is_null($this->mainObj));
    }

    protected function getMain()
    {
        $res = $this->mainObj;
        if (is_null($res)) {
            return $this;
        } else {
            return $res->getMain();
        }
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

// Autorize Actions

    public function getActionPath($action)
    {
        if (! $this->isAllowed($action)) {
            return null;
        }
        $path = $this->request->getActionPath($action);
        if (is_null($path)) {
            return null;
        }
        $path = $this->request->joinPathAction($path, $action);
        return $path;
    }

    protected function isAllowed($action)
    {
        if (! $this->checkActionObj($action)) {
            return false;
        }
        if ($this->home->isRoot() and $this->request->isRootPath()) {
            return false;
        }
//      if ($action == V_S_DELT) {
//          return $this->obj->isDel();
//      }
        return true;
    }
    
    public function getClassPath($mod, $action)
    {
        if (! $this->isAllowedMod($mod, $action)) {
            return null;
        }
        $path = $this->request->getClassPath($mod, $action);
        return $path;
    }
    
    
    protected function isAllowedMod($mod, $action)
    {
        if (! $this->isMain()) {
            return false;
        }
        $x = new Model($mod);
        return $this->home->canLink($x);
    }
        

    public function getCrefPath($attr, $action)
    {
        if (! $this->isAllowedCref($attr, $action)) {
            return null;
        }
        $path = $this->request->getCrefPath($attr, $action);
        return $path;
    }
        
    protected function isAllowedCref($attr, $action)
    {
        if (! $this->isMain()) {
            return false;
        }
        if ($this->request->getAction() != V_S_READ) {
            return false;
        }
        if (! $this->checkActionObj(V_S_UPDT)) {
            return false;
        }
        return true;
    }
   
// from req

    public function getReq()
    {
        return $this->getMain()->request;
    }

    public function getPath()
    {
        if (is_null($this->request)) {
            return null;
        }
        return $this->request->getPath();
    }
    
    public function getRPath()
    {
        if (is_null($this->request)) {
            return null;
        }
        return $this->request->getRPath();
    }

// Handle
 // in selection list
    public function getObjId($id)
    {
        $obj = new Model($this->obj->getModName(), (int) $id);
        $path = $this->getRPath().'/'.$id;
        $req = new Request($path, V_S_READ);
        $h= new Handle($req, $this->home, $obj, $this);
        return $h;
    }
  // in cref list
    public function getCref($attr, $id)
    {
        $obj = $this->obj->getCref($attr, (int) $id);
        $path = $this->getRPath().'/'.$attr.'/'.$id;
        if ($this->request->isRootPath()) {
            $path = '/'.$attr.'/'.$id;
        }
        $req = new Request($path, V_S_READ);
        $h= new Handle($req, $this->home, $obj, $this);
        return $h;
    }

    public function getCode($attr, $id)
    {
        $obj = $this->obj->getCode($attr, $id);
        $path = $this->getRefPath($obj);
        if (is_null($path)) {
            $req=null;
        } else {
            $req= new Request($path, V_S_READ);
        }
        $h= new Handle($req, $this->home, $obj, $this);
        return $h;
    }
    
    public function getRef($attr)
    {
        $obj = $this->obj->getRef($attr);
        if (is_null($obj)) {
            return null;
        }
        $path = $this->getRefPath($obj);
        if (is_null($path)) {
            $req=null;
        } else {
            $req= new Request($path, V_S_READ);
        }
        $h= new Handle($req, $this->home, $obj, $this);
        return $h;
    }
    
    protected function getRefPath($obj)
    {
        if (!is_null($this->mainObj)) {
            return ($this->mainObj->getRefPath($obj));
        }
        
        $mod= $obj->getModName();
        $id = $obj->getId();
        $path='/'.$mod.'/'.$id;
        $resN = $this->pathNrmArr;
        $res  = $this->request->pathArr();
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
        // to be reviewed
        if ($this->home->isRoot()) {
            return $path;
        }
        if ($this->home->isLinked($obj)) {
            return $path;
        }
        return null;
    }
     
// obj  : access should be controlled here 
    
    public function getAttrList()
    {
        return $this->obj->getAttrList();
    }

    public function getModName()
    {
        return $this->obj->getModName();
    }

    public function getId()
    {
        return $this->obj->getId();
    }
    
    public function getTyp($attr)
    {
        return $this->obj->getTyp($attr);
    }
    
    public function getVal($attr)
    {
        return $this->obj->getVal($attr);
    }
    
    public function getDflt($attr)
    {
        return $this->obj->getDflt($attr);
    }
    
    public function getValues($attr)
    {
        return $this->obj->getValues($attr);
    }
 
    public function getModCref($attr)
    {
        return $this->obj->getModCref($attr);
    }
   
    public function getAbstrNme()
    {
        return $this->obj->getAbstrNme();
    }
   
    public function getModRef($attr)
    {
        return $this->obj->getModRef($attr);
    }
    
    public function isProtected($attr)
    {
        return $this->obj->isProtected($attr);
    }
      
    public function isMdtr($attr)
    {
        return $this->obj->isMdtr($attr);
    }

    public function isEval($attr)
    {
        return $this->obj->isEval($attr);
    }
    
    public function isModif($attr)
    {
        return $this->obj->isModif($attr);
    }

    public function isSelect($attr)
    {
        return $this->obj->isSelect($attr);
    }

    public function setVal($attr, $val)
    {
        return $this->obj->setVal($attr, $val);
    }

    public function save()
    {
        return $this->obj->save();
    }

    public function setCriteria($attrL, $opL, $valL)
    {
        return $this->obj->setCriteria($attrL, $opL, $valL);
    }

    public function select()
    {
        return $this->obj->select();
    }
    
    public function delet()
    {
        $res = $this->obj->delet();
        return $res;
    }
    
    public function isErr()
    {
        return $this->obj->isErr();
    }

    public function getErrLog()
    {
        return $this->obj->getErrLog();
    }
}
