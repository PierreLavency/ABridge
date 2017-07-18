<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Mod\Model;

class UtilsC
{
   
    public static function initHandlers($name, $classes, $bsname)
    {
        $typs = ['dataBase','fileBase'];
        $bases = [];
        Handler::get()->resetHandlers();
        foreach ($typs as $typ) {
            $db = Handler::get()->getBase($typ, $name);
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
    
    public static function createMods($bd)
    {
        foreach ($bd as $CName => $PName) {
            $res = self::createMod($PName, $bd);
            if (!$res) {
                return false;
            }
        }
        $res = self::checkMods($bd);
        return $res;
    }
    
    
    public static function createMod($name, $bd)
    {
        $x = new Model($name);
        $x->deleteMod();
        $x->initMod($bd);
        $x->saveMod();
        if ($x->isErr()) {
            $log = $x->getErrLog();
            $log->show();
            return false;
        }
        return true;
    }
    
    public static function checkMods($bd)
    {
        foreach ($bd as $CName => $PName) {
            $x = new Model($PName);
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
