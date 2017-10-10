<?php
namespace ABridge\ABridge;

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
                'dataBase'=>'test',
                'fileBase'=>'test',
                'memBase'=>'test',
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822',
                'trace'=>'0',
        ];
        return $prm;
    }
}
