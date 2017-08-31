<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\CstMode;


use Exception;

class Access
{
    protected static function isRoot($session)
    {
        return (is_null($session->getVal('ActiveRole')));
    }

    protected static function matchEval($elm, $patrn)
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

    public static function checkReq($session, $req)
    {
        if (is_null($req)) {
            throw new Exception(CstError::E_ERC012);
        }
        $res= self::getCond($session, $req->getAction(), $req->getModpath());
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public static function getSelMenu($session, $classList)
    {
        $rList = [];
        foreach ($classList as $className) {
            $modPath = '|'.$className;
            $res = self::getCond($session, CstMode::V_S_SLCT, $modPath);
            if ($res === ['true']) {
                $rList[]= '/'.$className;
            }
        }

        return $rList;
    }
    
    protected static function getCond($session, $action, $modpath)
    {
        $obj = $session->getCobj();
        $roleSpec=$obj->getRSpec();
        if (!$roleSpec || !$session->getVal('Checked') || !$session->getVal('ValidFlag')) {
            $roleSpec=[
                    [CstMode::V_S_READ,'|','true'],
                    [[CstMode::V_S_UPDT,CstMode::V_S_READ],'|Session',['Session'=>'id']],
                    
            ];
        }
        $cond = [];
        foreach ($roleSpec as $elm) {
            if (self::matchEval($action, $elm[0]) and self::matchEval($modpath, $elm[1])) {
                $ncond = $elm[2];
                switch ($ncond) {
                    case 'true':
                        if (count($cond) == 0) {
                            $cond[]='true';
                        }
                        break;
                    case 'false':
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

    protected static function getCondAttr($cond, $attr)
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

    public static function checkARight($session, $req, $attrObjs, $protect, $plast = true)
    {
        if (is_null($req)) {
            throw new Exception(CstError::E_ERC012);
        }
        $action = $req->getAction();
        $modpath=$req->getModpath();
        $pathcond = self::getCond($session, $action, $modpath);
        if (!$pathcond) {
            return false;
        }
        $c=count($attrObjs);
        foreach ($attrObjs as $attrObj) {
            $attr = $attrObj[0];
            $obj = $attrObj[1];
            $c--;
            $last = (!$c and $plast);
            $res = self::checkAttrCond($session, $action, $pathcond, $attr, $obj, $protect, $last);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected static function checkAttrCond($session, $action, $pathcond, $attr, $obj, $protect, $last)
    {
        $cond = self::getCondAttr($pathcond, $attr);
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
            $res = self::checkLinkAttr($session, $action, $obj, $attro, $attrs, $protect, $last);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected static function checkLinkAttr($session, $action, $obj, $attroExp, $attrsExp, $protect, $last)
    {
        if (self::isRoot($session)) {
            return true;
        }
        $r = self::resolvePath($session, $attrsExp);
        if (is_null($r)) {
            return false;
        }
        $sess=$r[0];
        $attrs=$r[1];
        $r = self::resolvePath($obj, $attroExp);
        if (is_null($r)) {
            return false;
        }
        $obj=$r[0];
        $attro=$r[1];
        if (is_null($obj)) {
            return false;
        }
        if ((!$obj->existsAttr($attro)) or (!$sess->existsAttr($attrs))) {
            throw new Exception(CstError::E_ERC050.":$attro:$attrs");
        }
        $typ = $obj->getTyp($attro);
        $typs = $sess->getTyp($attrs);
        if (Mtype::baseType($typ) != Mtype::baseType($typs)) {
            throw new Exception(CstError::E_ERC050.':'.$typ.':'.$typs);
        }
        if ($typ == Mtype::M_REF
        and $typs == Mtype::M_REF
        and ($obj->getModRef($attro) != $sess->getModRef($attrs))) {
            throw new Exception(CstError::E_ERC050.':'.$obj->getModRef($attro).':'.$sess->getModRef($attrs));
        }
        $id1=$sess->getVal($attrs);
        $id2=$obj->getVal($attro);
        if ($id1 === $id2) {
            if ($protect) {
                $obj->protect($attro);
            }
            return true;
        }
        if (($action == CstMode::V_S_CREA or $action ==CstMode::V_S_SLCT) and is_null($id2) and $last) {
            if ($protect) {
                $obj->setVal($attro, $id1);
                $obj->protect($attro);
            }
            return true;
        }
        return false;
    }
    
    protected static function resolvePath($obj, $attrExp)
    {
        $attrA=explode(':', $attrExp);
        return self::resPath($obj, $attrA);
    }
    
    protected static function resPath($obj, $attrA)
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
        return self::resPath($obj, $attrA);
    }
}
