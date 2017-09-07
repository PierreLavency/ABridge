<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Mod\Model;

class UtilsC
{
   
    public static function initHandlers($name, $classes, $bsname, $prm)
    {
        $typs = ['dataBase','fileBase'];
        $bases = [];
        Handler::get()->resetHandlers();
        foreach ($typs as $typ) {
            $db = Handler::get()->setBase($typ, $name, $prm);
            $i=1;
            $bindings=[];
            foreach ($classes as $cname) {
                $bname = $bsname.'_'.$typ.'_'.$i;
                $i++;
                $bindings[$cname]=$bname;
                Handler::get()->setStateHandler($bname, $typ, $name);
            }
            $bases[]=[$db,$bindings];
        }
        return $bases;
    }
    
    public static function initClasses($bases)
    {
        foreach ($bases as $base) {
            list($db,$bd) = $base;
            
            $db->beginTrans();

            $res= self::createMods($bd);
            if (!$res) {
                return false;
            }
            $db->commit();
        }
        return true;
    }
    
    public static function createMods($bindings, $logicalNames = null)
    {
        $normBindings=self::bindingNorm($bindings);
        if (is_null($logicalNames)) {
            $logicalNames = array_keys($normBindings);
        }
        foreach ($logicalNames as $logicalName) {
            $res = self::createMod($logicalName, $normBindings);
            if (!$res) {
                return false;
            }
        }
        $res = self::checkMods($logicalNames, $normBindings);
        return $res;
    }
    
    protected static function bindingNorm($bindings)
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
    
    
    public static function createMod($logicalName, $normBindings)
    {
        $physicalName=$normBindings[$logicalName];
        $x = new Model($physicalName);
        $x->deleteMod();
        $x->initMod($normBindings);
        $x->saveMod();
        if ($x->isErr()) {
            echo  $physicalName ;
            $log = $x->getErrLog();
            $log->show();
            return false;
        }
        return true;
    }
    
    public static function checkMods($logicalNames, $normBindings)
    {
        foreach ($logicalNames as $logicalName) {
            $physicalName=$normBindings[$logicalName];
            $x = new Model($physicalName);
            $res = $x->checkMod();
            if (!$res) {
                $log = $x->getErrLog();
                $log->show();
                return false;
            }
        }
        return true;
    }
}
