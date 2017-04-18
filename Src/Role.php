<?php

class Role
{
    protected $spec = [];
    protected $session = null;
    
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
        $this->spec = [['true', 'true', 'true']];
        $this->session = null;
    }
 
    protected function construct2($spec, $session)
    {
        $this->spec = $spec;
        $this->session = $session;
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
    
    public function checkPath($action, $path)
    {
        $res= $this->getCond($action, $path);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
        
    protected function getCond($action, $path)
    {
        $spec = $this->spec;
        $cond = [];
        foreach ($spec as $elm) {
            if ($this->matchEval($action, $elm[0]) and $this->matchEval($path, $elm[1])) {
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

    public function getAttrCond($action, $path, $attr)
    {
        $cond=$this->getCond($action, $path);
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

    
    public function checkARight($action, $rpath, $attrObjs)
    {
        $pathcond = $this->getCond($action, $rpath);
        if (!$pathcond) {
            return false;
        }
        foreach ($attrObjs as $attrObj) {
            $attr = $attrObj[0];
            $obj = $attrObj[1];
            $res = $this->checkAttrCond($pathcond, $attr, $obj);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected function checkAttrCond($pathcond, $attr, $obj)
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
            $res = $this->checkLinkAttr($obj, $attro, $attrs);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected function checkLinkAttr($obj, $attro, $attrs)
    {
        $sess= $this->session;
        if (is_null($sess)) {
            return true;
        }
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
        if ($typ == M_REF and ($obj->getRefMod($attro) != $sess->getRefMod($attrs))) {
            return false;
        }
        $id1=$sess->getVal($attrs);
        $id2=$obj->getVal($attro);
        if ($id1 === $id2) {
            $obj->protect($attro);
            return true;
        }
        if ($obj->getId()==0 and is_null($id2)) {
            $obj->setVal($attro, $id1);
            $obj->protect($attro);
            return true;
        }
        return false;
    }
}
