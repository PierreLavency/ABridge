
<?php

require_once 'Request.php';
require_once 'SessionHdl.php';
require_once 'Model.php';

class Handle
{
    protected $sessionHdl=null;
    protected $request=null;
    protected $obj=null;
    protected $mainObj=null;
    protected $pathNrmArr=[];
    protected $attrObjs=[];
 
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = 'construct'.$i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    protected function construct1($sessionHdl)
    {
        $request = new Request();
        $this->initReq($request, $sessionHdl);
    }
    
    protected function construct2($path, $sessionHdl)
    {
        $request = new Request($path);
        $this->initReq($request, $sessionHdl);
    }
    
    protected function construct3($path, $action, $sessionHdl)
    {
        $request = new Request($path, $action);
        $this->initReq($request, $sessionHdl);
    }

    protected function initReq($request, $sessionHdl)
    {
        $this->request = $request;
        $this->sessionHdl = $sessionHdl;
        $res = $sessionHdl->checkReq($this->request);
        if (!$res) {
            throw new Exception(E_ERC049.':'.$this->request->getUrl());
        }
        $this->initObj();
    }
    
    protected function construct5($req, $sessionHdl, $objs, $obj, $main)
    {
        $this->request = $req;
        $this->sessionHdl = $sessionHdl;
        $this->attrObjs=$objs;
        foreach ($objs as $attrObj) {
            $this->pathNrmArr[]=$attrObj[1]->getModName();
            $this->pathNrmArr[]=$attrObj[1]->getId();
        }
        $this->obj = $obj;
        $this->mainObj = $main;
    }
    
    private function initObj()
    {
        $this->obj=null;
        $obj = null;
        if ($this->request->isRoot()) {
            return;
        }
        $this->pathNrmArr=[];
        $pathArr= $this->request->pathArr();
        $objN   = $this->request->objN();
        for ($i=0; $i<$objN; $i=$i+2) {
            $mod = $pathArr[$i];
            $id  = $pathArr[$i+1];
            if (is_null($obj)) {
                $obj = new Model($mod, $id);
            } else {
                $obj = $obj->getCref($mod, $id);
            }
            $this->pathNrmArr[]=$obj->getModName();
            $this->pathNrmArr[]=$obj->getId();
            $this->attrObjs[]=[$mod,$obj];
        }
        if ($this->request->isClassPath()) {
            $c = count($pathArr);
            $mod =  $pathArr[$c-1];
            if ($c == 1) {
                $obj = new Model($mod);
            } else {
                $obj = $obj->newCref($mod);
            }
            $this->pathNrmArr[]=$obj->getModName();
            $this->attrObjs[]=[$mod,$obj];
        }
        $this->obj =$obj;
        $res = $this->sessionHdl->checkARight($this->request, $this->attrObjs, true);
        if (!$res) {
            throw new Exception(E_ERC053.':'.$this->request->getUrl());
        }
    }
 
    public function getSessionHdl()
    {
        return $this->sessionHdl;
    }
    
    public function nullObj()
    {
        return (is_null($this->obj));
    }
    
    public function isMain()
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
        if ($cid === $rid and $cmod === $rmod and $rid!= 0) {
            return true;
        }
        $cmod = $main->getAbstrNme();
        if ($cid === $rid and $cmod === $rmod and $rid!= 0) {
            return true;
        }
        return false;
    }

// Autorize Actions on object

    public function getActionUrl($action, $prm = [])
    {
        // for object menu
        $req = $this->request->getActionReq($action);
        $res = $this->sessionHdl->checkARight($req, $this->attrObjs, false);
        if (!$res) {
            return null;
        }
        return $req->getUrl($prm);
    }

    public function getCrefUrl($attr, $action, $prm = [])
    {
        // for Cref menu
        $req= $this->request->getCrefReq($attr, $action);
        if (is_null($req)) {
            return null;
        }
        $res = $this->sessionHdl->checkARight($req, $this->attrObjs, false);
        if (!$res) {
            return null;
        }
        return $req->getUrl($prm);
    }
   
// Handle

    protected function newHdl($req, $sessionHdl, $objs, $obj, $robj)
    {
        $res = $this->sessionHdl->checkARight($req, $objs, false); // all access in read so false
        if (!$res) {
            return null;
        }
        $h= new Handle($req, $this->sessionHdl, $objs, $obj, $this);
        return $h;
    }

    public function getDD()
    {
        if ($this->request->isRoot()) {
            return $this;
        }
        $req = $this->request->popReq();
        if ($req ->isRoot()) {
            return $this->newHdl($req, $this->sessionHdl, [], null, $this);
        }
        $objs= $this->attrObjs;
        array_pop($objs);
        $obje=$objs[count($objs)-1];
        $obj=$obje[1];
        return $this->newHdl($req, $this->sessionHdl, $objs, $obj, $this);
    }
    
    
    public function getObjId($id)
    {
         // in selection list
        $req = $this->request->getObjReq($id, V_S_READ);
        $res = $this->sessionHdl->checkReq($req);
        if (!$res) {
            return null;
        }
        $mod=$this->obj->getModName();
        $obj = new Model($mod, (int) $id);
        $objs= $this->attrObjs;
        $lobjs= array_pop($objs);
        $objs[] = [$lobjs[0],$obj];
        return $this->newHdl($req, $this->sessionHdl, $objs, $obj, $this);
    }
    
    public function getCref($attr, $id)
    {
         // in cref list
        $req = $this->request->getCrefReq($attr, V_S_READ, $id);
        $res = $this->sessionHdl->checkReq($req);
        if (!$res) {
            return null;
        }
        $obj = $this->obj->getCref($attr, (int) $id);
        $objs = $this->attrObjs;
        $objs[]=[$attr,$obj];
        return $this->newHdl($req, $this->sessionHdl, $objs, $obj, $this);
    }

    public function getCode($attr, $id)
    {
        $obj = $this->obj->getCode($attr, $id);
        return $this->getRefHdl($obj);
    }
    
    public function getRef($attr)
    {
        $obj = $this->obj->getRef($attr);
        return $this->getRefHdl($obj);
    }
    
    protected function getRefHdl($obj)
    {
        if (is_null($obj)) {
            return null;
        }

        $mod= $obj->getModName();
        $id = $obj->getId();
        $resN = $this->pathNrmArr;
        $res  = $this->request->pathArr();
        $objs = $this->attrObjs;
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
                array_pop($objs);
            }
        }
        if ($found) {
            $path = '/'.implode('/', $res);
        } else {
            $path ='/'.$mod.'/'.$id;
            $objs[]=[$mod,$obj];
        }
        $req = new Request($path, V_S_READ);
        return $this->newHdl($req, $this->sessionHdl, $objs, $obj, $this);
    }
     
// obj  : access should be controlled here 
    
    public function getAttrList()
    {
        return $this->obj->getAttrList();
    }
    
    public function existsAttr($attr)
    {
        return $this->obj->existsAttr($attr);
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

    public function isOneCref($attr)
    {
        return $this->obj->isOneCref($attr);
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
        // check action
        return $this->obj->setVal($attr, $val);
    }

    public function save()
    {
        $pid = $this->obj->getId();
        $id = $this->obj->save();
        // check action and popid ? shoudl check access rights
        if (($id) and $pid != $id) {
            $this->request->pushId($id);
        }
        return $id;
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
        // check action
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

// from req


    public function setAction($action)
    {
        // check rights !
        return  $this->getMain()->request->setAction($action);
    }

    public function getDocRoot()
    {
        return $this->request->getDocRoot();
    }
    
    public function getRPath()
    {
        return $this->request->getRPath();
    }

    public function getAction()
    {
        return  $this->request->getAction();
    }
 
    public function getUrl($Prm = [])
    {
        return $this->request->getUrl($Prm);
    }

    public function getMethod()
    {
        return $this->request->getMethod();
    }
    
    public function getPrm($attr, $raw = false)
    {
        return $this->request->getPrm($attr, $raw);
    }
}
