<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\CstMode;


use Exception;

class Access
{
    
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
        $res= self::getCondPath($session, $req->getAction(), $req->getModpath());
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
            $res = self::getCondPath($session, CstMode::V_S_SLCT, $modPath);
            if ($res === true) {
                $rList[]= '/'.$className;
            }
        }

        return $rList;
    }
    
    protected static function getCondPath($session, $action, $modpath)
    {
        $sessionCobj = $session->getCobj();
        $roleSpec=$sessionCobj->getRSpec();
        if (!$roleSpec || !$session->getVal('Checked') || !$session->getVal('ValidFlag')) {
            $roleSpec=[
                    [CstMode::V_S_READ,'|','true'],
                    [[CstMode::V_S_UPDT,CstMode::V_S_READ],'|Session',['Session'=>'id']],
                    
            ];
        }
        $pathCond = [];
        $found = false;
        foreach ($roleSpec as $elm) {
            if (self::matchEval($action, $elm[0]) and self::matchEval($modpath, $elm[1])) {
                $pathCondElm = $elm[2];
                switch ($pathCondElm) {
                    case 'true':
                        $found=true;
                        break;
                    case 'false':
                        return false;
                        break;
                    default:
                        $found=true;
                        $pathCond[]=$pathCondElm;
                }
            }
        }
        if (!$found) {
            return false;
        }
        if ($pathCond === []) {
            return true;
        }
        return $pathCond;
    }

    protected static function getCondAttr($pathCond, $attr)
    {
        $attrCond = [];
        if ($pathCond === true) {
            return true;
        }
        foreach ($pathCond as $pathCondElm) {
            if (isset($pathCondElm[$attr])) {
                $attrCond[]=$pathCondElm[$attr];
            }
        }
        if ($attrCond===[]) {
            return true;
        }
        return $attrCond;
    }

    public static function checkARight($session, $req, $attrObjs, $protect, $plast = true)
    {
        if (is_null($req)) {
            throw new Exception(CstError::E_ERC012);
        }
        $action = $req->getAction();
        $modpath=$req->getModpath();
        $pathcond = self::getCondPath($session, $action, $modpath);
        if (!$pathcond) {
            return false;
        }
        $c=count($attrObjs);
        foreach ($attrObjs as $attrObj) {
            $attr = $attrObj[0];
            $obj = $attrObj[1];
            $c--;
            $last = (!$c and $plast);
            $attrCond = self::getCondAttr($pathcond, $attr);
            $res = self::checkAttrCond($session, $action, $attrCond, $obj, $protect, $last);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected static function checkAttrCond($session, $action, $attrCond, $obj, $protect, $last)
    {
        if ($attrCond === true) {
            return true;
        }
        foreach ($attrCond as $attrCondElm) {
            $attra = explode('<>', $attrCondElm);
            if (count($attra) > 1) {
                $objAttrPath= $attra[0];
                $sessAttrPath=$attra[1];
            } else {
                $sessAttrPath=$attrCondElm;
                $objAttrPath=$attrCondElm;
            }
            $res = self::checkLinkAttr($session, $action, $obj, $objAttrPath, $sessAttrPath, $protect, $last);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    protected static function checkLinkAttr($session, $action, $obj, $objAttrPath, $sessAttrPath, $protect, $last)
    {
        $attrA=explode(':', $objAttrPath);
        $attr=$attrA[0];
        $objVal = self::getAttrPathArrayVal($obj, $attrA);
        $sessVal= self::getAttrPathVal($session, $sessAttrPath);
        if ($sessVal === $objVal) {
            if ($protect) {
                $obj->protect($attr);
            }
            return true;
        }
        if (($action == CstMode::V_S_CREA or $action ==CstMode::V_S_SLCT) and is_null($objVal) and $last) {
            if ($protect) {
                $obj->setVal($attr, $sessVal);
                $obj->protect($attr);
            }
            return true;
        }
        return false;
    }
    
    public static function getAttrPathVal($obj, $attrPath)
    {
        $attrA=explode(':', $attrPath);
        return self::getAttrPathArrayVal($obj, $attrA);
    }
    
    
    protected static function getAttrPathArrayVal($obj, $attrA)
    {
        if (is_null($obj)) {
            throw new Exception(CstError::E_ERC050);
        }
        $c = count($attrA);
        if ($c==1) {
            $res = $obj->getVal($attrA[0]);
            return $res;
        }
        $attr = array_shift($attrA);
        $obj->protect($attr);
        $obj=$obj->getRef($attr);
        return self::getAttrPathArrayVal($obj, $attrA);
    }
}
