<?php

use ABridge\ABridge\Apps\Cdv;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class Cdv_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        Mod::reset();
        $classes = [Cdv::CODE,Cdv::CODEVAL];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        
        $codeName=$prm['fileBase'][cdv::CODE];
        $codeVal=$prm['fileBase'][cdv::CODEVAL];
        $config = [
                cdv::CODE=>$codeName,
                cdv::CODEVAL=>$codeVal,
                Cdv::CODELIST=>['test'],
                Cdv::CODEDATA=>['test'=>['x']],
                
        ];
        
        $cdv = new Cdv($prm['application'], $config);
        $cdv->init();
        
        $mod=Mod::get();
        
        $mod->begin();
        
        $res= $cdv->initMeta();
        $this->assertEquals($prm['fileBase'][cdv::CODE].'/1', $res['test']);
  
        
        $res= $cdv->initData();
        $this->assertEquals([1], $res);
        
        $mod->end();
    }
}
