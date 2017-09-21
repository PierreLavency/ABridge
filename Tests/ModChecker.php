<?php

use ABridge\ABridge\Controler;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\View\Vew;

require_once 'C:/Users/pierr/ABridge/Src/ABridge_test.php';

$baseTypeList=['dataBase','fileBase'];
//$baseList=['ABB','ABBTEST','ADM','AFS','ALB','CDV','CSN','GEN','UCL','UCL_TEST','USR',];
$baseList=['UCL','UCL_TEST',];
$config =
[
        'Handlers'=>[],
];


$ini =
[
        'name'=>'test_perf',
        'base'=>'dataBase',
        'dataBase'=>'test_perf',
        'fileBase'=>'test_perf',
        'memBase '=>'test_perf',
        'path'=>'C:/Users/pierr/ABridge/Datastore/',
        'host'=>'localhost',
        'user'=>'cl822',
        'pass'=>'cl822',
        'trace'=>'0',
        
];

Log::reset();
Mod::reset();
Hdl::reset();
Usr::reset();
Adm::reset();
Vew::reset();

foreach ($baseList as $baseName) {
    echo $baseName."\n";
    foreach ($baseTypeList as $baseType) {
        $className=$baseName.'_'.$baseType.'_'.__FILE__;
        $config['Handlers']=[$className=>[$baseType]];
        $ini[$baseType]=$baseName;
        $ctrl = new Controler($config, $ini);
        $stateHandler=Mod::get()->getStateHandler($className);
        $handlerState = $stateHandler->showState();
        echo "\t".$baseType."\n";
        foreach ($handlerState as $modName => $modState) {
            echo "\t\t".$modName."\n";
//			echo "\t\t".json_encode($modState)."\n";
            $config['Handlers']=[$modName=>[$baseType]];
            $ctrl = new Controler($config, $ini);
            $stateHandler=Mod::get()->getStateHandler($modName);
            $mod=new Model($modName);
            echo "\t\t".json_encode($mod->getAttrList())."\n";
        }
    }
}
