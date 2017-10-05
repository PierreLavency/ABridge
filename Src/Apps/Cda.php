<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\App;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\View\CstView;

class Cda extends App
{
    const CODE='AbstractCode';
    const CODELIST='CodeList';
    const CODEDATA='CodeData';
    
    public static function init($prm, $config)
    {
        $handlerList= [];
        $viewList = [];
        
        $code = self::CODE;
        if (isset($config[self::CODE])) {
            $code = $config[self::CODE];
        }
        $handlerList[$code]=[];
        
        $codelist = $config[self::CODELIST];
        foreach ($codelist as $codeName) {
            $handlerList[$codeName]=[];
            $viewList[$codeName]=['attrList' => [CstView::V_S_REF=> ['Value']]];
        }
        
        $res = [
                
                'Handlers' => $handlerList,
                'View' => $viewList,
        ];
        return $res;
    }
    
    
    public static function initMeta($config)
    {
        $bindings=[];
        $code = self::CODE;
        if (isset($config[self::CODE])) {
            $code = $config[self::CODE];
        }

        // Abstract
        
        $obj = new Model($code);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Value', Mtype::M_STRING);
        $res=$obj->setProp('Value', Model::P_MDT);
        $res = $obj->setProp('Value', Model::P_BKY);
        $res = $obj->setAbstr();
        
        $res = $obj->saveMod();
        echo $obj->getModName()."<br>";
        $obj->getErrLog()->show();
        echo "<br>";

        $bindings[self::CODE]=$code;
        
        
        // Concretes
        
        $codelist = [];
        if (isset($config[self::CODELIST])) {
            $codelist= $config[self::CODELIST];
        }
        
        foreach ($codelist as $codeName) {
            $obj = new Model($codeName);
            $res= $obj->deleteMod();
            $res = $obj->setInhNme($code);
            $res = $obj->saveMod();
            echo $obj->getModName()."<br>";
            $obj->getErrLog()->show();
            echo "<br>";
            $bindings[$codeName]=$codeName;
        }
        
        return $bindings;
    }
    
    public static function initData($prm)
    {
        $codeData=$prm[self::CODEDATA];
        foreach ($codeData as $codeName => $values) {
            foreach ($values as $value) {
                $valMobj= new Model($codeName);
                $valMobj->setVal('Value', $value);
                $valMobj->save();
            }
        }
    }
}
