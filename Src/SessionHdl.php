<?php

class SessionHdl
{
    protected $roleSpec = [];
    protected $session = null;
    protected $isRoot = false;
    
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
        $this->isRoot = true;
        $this->roleSpec=[['true', 'true', 'true']];
        $this->session=null;
    }
 
    public function refresh()
    {
        $this->construct1($this->session);
    }
 
 
    protected function construct1($session)
    {
         $role = $session->getRef('Role');
         $this->construct2($session, $role);
    }
 
    protected function construct2($session, $role)
    {
        $this->roleSpec=[];
        $this->session = $session;
        $this->isRoot=false;
        if (is_null($session)) {
            $this->isRoot = true;
            $this->roleSpec=[['true', 'true', 'true']];
            return;
        }
        $user = $session->getVal('User');
        if (is_null($user)) {
            $this->isRoot = true;
            $this->roleSpec=[['true', 'true', 'true']];
            return;
        }
        if (! is_null($role)) {
            $res = $role->getVal('Spec');
            $val = json_decode($res, true);
//					var_dump($val);
            if (! is_null('Spec')) {
                $this->roleSpec= json_decode($res, true);
            }
        }
    }
    
    public function isRoot()
    {
        return ($this->isRoot);
    }
    
    protected function matchEval($elm, $patrn)
    {
        if (is_array($patrn)) {
            return in_array($elm, $patrn, true);
        }
        if ($patrn === 'true') {
            return true;
        }
        if ($patrn === $elm) {
            return true;
        }
        return false;
    }

    public function checkReq($req)
    {
        if (is_null($req)) {
            return false;
        }
        $res= $this->getCond($req->getAction(), $req->getModpath());
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
        
    protected function getCond($action, $modpath)
    {
//		echo ' '.$action.':'.$modpath."<br>";
        $roleSpec = $this->roleSpec;
        $cond = [];
        foreach ($roleSpec as $elm) {
            if ($this->matchEval($action, $elm[0]) and $this->matchEval($modpath, $elm[1])) {
                $ncond = $elm[2];
                switch ($ncond) {
                    case 'true':
                        if (count($cond) == 0) {
                            $cond[]='true';
                        }
                        break;
                    case 'false':
                        $this->cond = [];
                        return false;
                        break;
                    default:
                        if (count($cond) == 1 and $cond[0] === 'true') {
                            $cond[0]=$ncond;
                        } else {
                            $cond[]=$ncond;
                        }
                }
            }
        }
        if ($cond == []) {
            return false;
        }
        $cond = array_unique($cond);
        return $cond;
    }

    public function getReqCond($req, $attr)
    {
        $cond=$this->getCond($req->getAction(), $req->getModpath());
        if (! $cond) {
            return false;
        }
        return $this->getCondAttr($cond, $attr);
    }
    
    protected function getCondAttr($cond, $attr)
    {
        $condElm = [];
        if ($cond === ['true']) {
            return $cond;
        }
        foreach ($cond as $condE) {
            if (isset($condE[$attr])) {
                $condElm[]=$condE[$attr];
            }
        }
        if ($condElm==[]) {
            $condElm=['true'];
        }
        return $condElm;
    }

    public function checkARight($req, $attrObjs, $protect = true)
    {
        if (is_null($req)) {
            return false;
        }
        $action = $req->getAction();
        $modpath=$req->getModpath();
        $pathcond = $this->getCond($action, $modpath);
        if (!$pathcond) {
            return false;
        }
        foreach ($attrObjs as $attrObj) {
            $attr = $attrObj[0];
            $obj = $attrObj[1];
            $res = $this->checkAttrCond($pathcond, $attr, $obj, $protect);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected function checkAttrCond($pathcond, $attr, $obj, $protect)
    {
        $cond = $this->getCondAttr($pathcond, $attr);
        if ($cond == ['true']) {
            return true;
        }
        foreach ($cond as $attro) {
            $attra = explode(':', $attro);
            $attrs=$attro;
            if (count($attra) > 1) {
                $attro= $attra[0];
                $attrs=$attra[1];
            }
            $res = $this->checkLinkAttr($obj, $attro, $attrs, $protect);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected function checkLinkAttr($obj, $attro, $attrs, $protect)
    {
        if ($this->isRoot) {
            return true;
        }
        $sess= $this->session;
        if (is_null($obj)) {
            return false;
        }
        if ((!$obj->existsAttr($attro)) or (!$sess->existsAttr($attrs))) {
            return false;
        }
        $typ = $obj->getTyp($attro);
        $typs = $sess->getTyp($attrs);
        if (baseType($typ) != baseType($typs)) {
            return false;
        }
        if ($typ == M_REF
        and $typs == M_REF
        and ($obj->getModRef($attro) != $sess->getModRef($attrs))
        ) {
            return false;
        }
        $id1=$sess->getVal($attrs);
        $id2=$obj->getVal($attro);
        if ($id1 === $id2) {
            if ($protect) {
                $obj->protect($attro);
            }
            return true;
        }
        if ($obj->getId()==0 and is_null($id2)) {
            if ($protect) {
                $obj->setVal($attro, $id1);
                $obj->protect($attro);
            }
            return true;
        }
        return false;
    }
}
