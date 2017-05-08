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
        if (is_null($user) or is_null($role)) {
            $this->isRoot = true;
            $this->roleSpec=[['true', 'true', 'true']];
            return;
        }
        $res = $role->getVal('JSpec');
        $val = json_decode($res, true);
        if (! is_null($val)) {
            $this->roleSpec= $val;
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
            $res = $this->checkAttrCond($action, $pathcond, $attr, $obj, $protect);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected function checkAttrCond($action, $pathcond, $attr, $obj, $protect)
    {
        $cond = $this->getCondAttr($pathcond, $attr);
        if ($cond == ['true']) {
            return true;
        }
        foreach ($cond as $attro) {
            $attra = explode('<>', $attro);
            $attrs=$attro;
            if (count($attra) > 1) {
                $attro= $attra[0];
                $attrs=$attra[1];
            }
            $res = $this->checkLinkAttr($action, $obj, $attro, $attrs, $protect);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected function checkLinkAttr($action, $obj, $attroExp, $attrsExp, $protect)
    {
        if ($this->isRoot) {
            return true;
        }

        $r = $this->resolvePath($this->session, $attrsExp);
        if (is_null($r)) {
            return false;
        }
        $sess=$r[0];
        $attrs=$r[1];
        
        $r = $this->resolvePath($obj, $attroExp);
        if (is_null($r)) {
            return false;
        }
        $obj=$r[0];
        $attro=$r[1];
        
        if (is_null($obj)) {
            return false;
        }
        if ((!$obj->existsAttr($attro)) or (!$sess->existsAttr($attrs))) {
            return false; // exception
        }
        $typ = $obj->getTyp($attro);
        $typs = $sess->getTyp($attrs);
        if (baseType($typ) != baseType($typs)) {
            return false; // Execption
        }
        if ($typ == M_REF
        and $typs == M_REF
        and ($obj->getModRef($attro) != $sess->getModRef($attrs))
        ) {
            return false;// Execption
        }
        $id1=$sess->getVal($attrs);
        $id2=$obj->getVal($attro);
        if ($id1 === $id2) {
            if ($protect) {
                $obj->protect($attro);
            }
            return true;
        }
        if (($action == V_S_CREA or $action ==V_S_SLCT) and is_null($id2)) {
            if ($protect) {
                $obj->setVal($attro, $id1);
                $obj->protect($attro);
            }
            return true;
        }
        return false;
    }
    

    protected function resolvePath($obj, $attrExp)
    {
        $attrA=explode(':', $attrExp);
        return $this->resPath($obj, $attrA);
    }
    
    protected function resPath($obj, $attrA)
    {
        if (is_null($obj)) {
            return null;
        }
        $c = count($attrA);
        if ($c==0) {
            return null;
        }
        $attr = array_shift($attrA);
        if ($c==1) {
            return [$obj,$attr];
        }
        $obj->protect($attr);
        $obj=$obj->getRef($attr);
        return $this->resPath($obj, $attrA);
    }
}
