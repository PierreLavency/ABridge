<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\AppComp;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\Mod\ModUtils;

class Cda extends AppComp
{
    const CODE='AbstractCode';
    const CODELIST='CodeList';
    const CODEDATA='CodeData';
    
    public function __construct($prm, $bindings)
    {
        $this->bindings=$bindings;
        $this->prm = $prm;
        $handlerList= [];
        $viewList = [];
        
        $code = self::CODE;
        if (isset($bindings[self::CODE])) {
            $code = $bindings[self::CODE];
        }
        $handlerList[$code]=[];
        
        $codelist = $bindings[self::CODELIST];
        $codeList = ModUtils::normBindings($codelist);
        foreach ($codelist as $logicalName => $codeName) {
            $handlerList[$codeName]=[];
            $viewList[$codeName]=['attrList' => [CstView::V_S_REF=> ['Value']]];
        }
        
        $this->config = [
                'Handlers' => $handlerList,
                'View' => $viewList,
        ];
    }
    
    
    public function initOwnMeta($prm)
    {
        $bindings=$this->bindings;
        $code = self::CODE;
        if (isset($bindings[self::CODE])) {
            $code = $bindings[self::CODE];
        }

        // Abstract
        
        $obj = new Model($code);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Value', Mtype::M_STRING);
        $res=$obj->setProp('Value', Model::P_MDT);
        $res = $obj->setProp('Value', Model::P_BKY);
        $res = $obj->setAbstr();
        
        $res = $obj->saveMod();
        $obj->getErrLog()->show();
        
        // Concretes
        
        $codelist = [];
        if (isset($bindings[self::CODELIST])) {
            $codelist= $bindings[self::CODELIST];
        }
        
        $codeList = ModUtils::normBindings($codelist);
        
        foreach ($codelist as $logicalName => $codeName) {
            $obj = new Model($codeName);
            $res= $obj->deleteMod();
            $res = $obj->setInhNme($code);
            $res = $obj->saveMod();
            $obj->getErrLog()->show();
        }
        
        $codelist[self::CODE]=$code;
        return $codelist;
    }
    
    public function initOwnData($prm)
    {
        $i=0;
        $codeData=$this->bindings[self::CODEDATA];
        foreach ($codeData as $codeName => $values) {
            foreach ($values as $value) {
                $valMobj= new Model($codeName);
                $valMobj->setVal('Value', $value);
                $valMobj->save();
                $i++;
            }
        }
        return [$i];
    }
}
