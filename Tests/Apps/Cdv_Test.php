<?php

use ABridge\ABridge\Apps\Cdv;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class Cdv_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
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
        
        $res = Cdv::init($prm, $config);
        
        $this->assertEquals([], $res['Handlers'][$codeName]);

        Mod::reset();
               
        $mod= Mod::get();
        
        $mod->init($prm['application'], $res['Handlers']);
        
        $mod->begin();
        
        $res= cdv::initMeta($config);
        $this->assertEquals($prm['fileBase'][cdv::CODE].'/1', $res['test']);
  
        
        $res= cdv::initData($config);
        $this->assertEquals(1, $res);
        
        $mod->end();
    }
}
