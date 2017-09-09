<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Mod\Model;

class UtilsC
{
    public static function genPrm($logicalNameList, $baseName, $types = ['dataBase','fileBase'])
    {
        $prm=[];
        $physicalNameList=[];
        $handlerList=[];
        $listBindings= [];
        foreach ($types as $type) {
            $bindings= [];
            foreach ($logicalNameList as $logicalName) {
                $physicalName = $baseName.'_'.$type.'_'.$logicalName;
                $bindings[$logicalName]=$physicalName;
                $physicalNameList[]=$physicalName;
                $handlerList[$physicalName]=[$type];
            }
            $prm[$type]=$bindings;
            $listBindings[]=$bindings;
        }
        $prm['handlers']=$handlerList;
        $prm['names']=$physicalNameList;
        $prm['bindL']=$listBindings;
        $prm['application']=
        [
                'name'=>'test',
                'base'=>'dataBase',
                'dbnm'=>'test',
                'flnm'=>'test',
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];
        return $prm;
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
    
    private static function bindingNorm($bindings)
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
