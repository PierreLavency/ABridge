<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Model;
use Exception;

class ModUtils
{
    
    public static function defltHandlers($bindings)
    {
        $defltHandlers=[];
        foreach ($bindings as $logicalName => $physicalName) {
            $defltHandlers[$physicalName]=[];
        }
        return $defltHandlers;
    }
    
    public static function normBindings($bindings)
    {
        $normBindings=[];
        foreach ($bindings as $logicalName => $physicalName) {
            if (is_numeric($logicalName)) {
                $normBindings[$physicalName]=$physicalName;
            } else {
                $normBindings[$logicalName]=$physicalName;
            }
        }
        return $normBindings;
    }
    
    public static function initModBindings($bindings, $logicalModNames = null)
    {
        $show=false;
        $res=true;
    
        $normBindings=self::normBindings($bindings);
        if (is_null($logicalModNames)) {
            $logicalModNames = array_keys($normBindings);
        }
        foreach ($logicalModNames as $logicalName) {
            $res = ($res and self::initModBinding($logicalName, $normBindings, $show));
        }
        if ($res) {
            $res = ($res and self::checkMods($logicalModNames, $normBindings, $show));
        }
        return $res;
    }
    
    
    public static function initModBinding($logicalModName, $normBindings, $show = false)
    {
        $physicalModName=$normBindings[$logicalModName];
        $x = new Model($physicalModName);
        $x->deleteMod();
        $x->initMod($normBindings);
        $x->saveMod();
        if ($x->isErr()) {
            if ($show) {
                $x->getErrLog()->show();
            }
            return false;
        }
        return true;
    }
    
    public static function checkMods($logicalModNames, $normBindings, $show = false)
    {
        $checkMods= true;
        foreach ($logicalModNames as $logicalModName) {
            $physicalModName=$normBindings[$logicalModName];
            $x = new Model($physicalModName);
            $checkMod= self::checkMod($x, $show);
            $checkMods= ($checkMods and $checkMod);
        }
        return $checkMods;
    }
    
    public static function checkMod($mod, $show = false)
    {
        $checkMod = true;
        foreach ($mod->getAllAttr() as $attr) {
            if (! $mod->isPredef($attr)) {
                $checkMod = ($checkMod and self::checkModAttr($mod, $attr));
            }
        }
        if (!$checkMod and $show) {
            $mod->getErrLog()->show();
        }
        return $checkMod;
    }
    
    public static function checkModAttr($mod, $attr)
    {
        $typ = $mod->getTyp($attr);
        switch ($typ) {
            case Mtype::M_REF:
                $res= self::checkRef($mod, $attr, $typ);
                break;
            case Mtype::M_CREF:
                $res=self::checkCref($mod, $attr, $typ);
                break;
            case Mtype::M_CODE:
                $res=self::checkCode($mod, $attr, $typ);
                break;
            default:
                $res= true;
        }

        $custumClass = Mod::get()->getClassMod($mod->getModName());
        if ($mod->isProp($attr, Model::P_EVL) and ! $custumClass) {
            $mod->getErrLog()->logLine(CstError::E_ERC061.':'.$attr);
            $res=false;
        }
        return $res;
    }

     
    protected static function checkCode($mod, $attr, $typ)
    {
        $parm= $mod->getParm($attr);
        $path=explode('/', $parm);
        $c = count($path)-1;
        switch ($c) {
            case 1:
                /* 			/ClassName 			*/
                $modName = $path[1];
                $obj = new Model($modName);
                if (!$obj->getStateHandler()) {
                    $mod->getErrLog()->logLine(CstError::E_ERC014.':'.$attr.':'.$typ.':'.$modName);
                    return false;
                }
                break;
            case 2:
                /* 			/./CrefAttr 		*/
                if ($path[1] != ".") {
                    $mod->getErrLog()->logLine(CstError::E_ERC020.':'.$attr.':'.$path[1]);
                    return false;
                }
                $atyp= $mod->getTyp($path[2]);
                if ($atyp != Mtype::M_CREF) {
                    $mod->getErrLog()->logLine(CstError::E_ERC055.':'.$path[2]);
                    return false;
                }
                break;
            case 3:
                /*		 /ClassName/Id/CRefAttr 	*/
                $modName=$path[1];
                $modId=(int) $path[2];
                $modCref=$path[3];
                try {
                    $obj = new Model($modName, $modId);
                } catch (Exception $e) {
                    $mod->getErrLog()->logLine($e->getMessage());
                    return false;
                }
                $atyp= $obj->getTyp($modCref);
                if ($atyp != Mtype::M_CREF) {
                    $mod->getErrLog()->logLine(CstError::E_ERC055.':'.$modCref);
                    return false;
                }
                break;
        }
        return true;
    }

    protected static function checkRef($mod, $attr, $typ)
    {
        $parm= $mod->getParm($attr);
        $path=explode('/', $parm);
        $c = count($path)-1;

       /* 			/ClassName 			*/
        $modName = $path[1];
        $obj = new Model($modName);
        if (!$obj->getStateHandler()) {
            $mod->getErrLog()->logLine(CstError::E_ERC014.':'.$attr.':'.$typ.':'.$modName);
            return false;
        }
        return true;
    }

    protected static function checkCref($mod, $attr, $typ)
    {
        $parm= $mod->getParm($attr);
        $path=explode('/', $parm);
        $modName = $path[1];
        $modRef=$path[2];
        $obj = new Model($modName);
        if (!$obj->getStateHandler()) {
            $mod->getErrLog()->logLine(CstError::E_ERC014.':'.$attr.':'.$typ.':'.$modName);
            return false;
        }
        $atyp= $obj->getTyp($modRef);
        if ($atyp != Mtype::M_REF) {
            $mod->getErrLog()->logLine(CstError::E_ERC054.':'.$modRef);
            return false;
        }
        return true;
    }
}
