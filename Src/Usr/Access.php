<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\CstError;

use ABridge\ABridge\Hdl\CstMode;


use Exception;

class Access
{
    
    protected static function matchEval($elm, $patrn)
    {
        if ($patrn === 'true') {
            return true;
        }
        if ($patrn === $elm) {
            return true;
        }
        if (is_array($patrn)) {
            return in_array($elm, $patrn, true);
        }
        return false;
    }

    public static function checkReq($session, $req)
    {
        if (is_null($req)) {
            throw new Exception(CstError::E_ERC012);
        };
        $res= self::getCondPath($session, $req->getAction(), $req->getModpath());
        if ($res) {
            return true;
        }
        return false;
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
        if (!$sessionCobj->hasRole()) {
            return true;
        }
        $roleSpec=$sessionCobj->getRSpec();
        if (!$roleSpec || !$session->getVal('Checked') || !$session->getVal('ValidFlag')) {
            $roleSpec=[
                    [CstMode::V_S_READ,'|','true'],
                    [[CstMode::V_S_UPDT,CstMode::V_S_READ],'|Session',['Session'=>':id']],
                    
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
            $parseCond = self::parseCond($attrCondElm);
            $objAttrPath= $parseCond[0];
            $condAttrPath=$parseCond[1];
            $sessAttrPath=$parseCond[2];
            $res = self::checkLinkAttr(
                $session,
                $action,
                $obj,
                $objAttrPath,
                $sessAttrPath,
                $condAttrPath,
                $protect,
                $last
            );
            if (!$res) {
                return false;
            }
        }
        return true;
    }
    
    
    private static function parseCond($attrCondElm)
    {
        $defltOp='==';
        $parseCond=[];
        $str = explode('<', $attrCondElm);
        if (count($str)==1) {
            $parseCond[] = $attrCondElm;
            $parseCond[] = $defltOp;
            $parseCond[] = $attrCondElm;
            return $parseCond;
        }
        if (count($str) == 2 and $str[0] != "") {
            $parseCond[]=$str[0];
            $str = explode('>', $str[1]);
            if (count($str)==2) {
                $opr = $str[0];
                if ($opr == "") {
                    $opr=$defltOp;
                }
                $parseCond[]=$opr;
                $parseCond[]=$str[1];
                return $parseCond;
            }
        }
        throw new Exception(CstError::E_ERC065.':'.$attrCondElm);
    }
     
    
    private static function evalCond($sessVal, $opr, $objVal)
    {
        switch ($opr) {
            case '==':
                return ($sessVal == $objVal);
            break;
            case '!=':
                return ($sessVal != $objVal);
            break;
            default:
                throw new Exception(CstError::E_ERC066.':'.$opr);
        }
    }
    
    protected static function checkLinkAttr($session, $action, $obj, $objAttrPath, $sessAttrPath, $opr, $protect, $last)
    {
        $attrPathList=explode(':', $objAttrPath);
        if ($attrPathList[0] != "") {
            throw new Exception(CstError::E_ERC051.':'.$objAttrPath);
        }
        $attr=$attrPathList[1];
 
        $objVal = self::getAttrPathArrayVal($obj, $attrPathList, $objAttrPath);
        $sessVal= self::getAttrPathVal($session, $sessAttrPath);
        
        if (self::evalCond($sessVal, $opr, $objVal)) {
            if ($protect) {
                $obj->protect($attr);
            }
            return true;
        }
        if ((
                $action == CstMode::V_S_CREA or $action ==CstMode::V_S_SLCT)
                and is_null($objVal) and $last and $opr == '==') {
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
        $attrPathList=explode(':', $attrPath);
        if ($attrPathList[0] != "") {
            return $attrPath;
        }
        return self::getAttrPathArrayVal($obj, $attrPathList, $attrPath);
    }
    
    
    protected static function getAttrPathArrayVal($obj, $attrPathList, $attrPath)
    {
        if (is_null($obj)) {
            throw new Exception(CstError::E_ERC050);
        }
        $c = count($attrPathList);
        if (! $c) {
            throw new Exception(CstError::E_ERC051.':'.$attrPath);
        }
        $attr = array_shift($attrPathList);
        if ($attr == "") {
            return self::getAttrPathArrayVal($obj, $attrPathList, $attrPath);
        }
        if ($c==1) {
            $res = $obj->getVal($attr);
            return $res;
        }
        $obj->protect($attr);
        $obj=$obj->getRef($attr);
        return self::getAttrPathArrayVal($obj, $attrPathList, $attrPath);
    }
}
