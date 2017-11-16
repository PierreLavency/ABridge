<?php

use ABridge\ABridge\Apps\Cda;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class Cda_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        Mod::reset();
        $classes = [Cda::CODE,'test'];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        
        $testName=$prm['fileBase']['test'];
        $codeName=$prm['fileBase'][cda::CODE];
        $config = [
                cda::CODE=>$codeName,
                Cda::CODELIST=>['test'=>$testName],
                Cda::CODEDATA=>[$testName=>['x']],
                
        ];
        
        $cda = new Cda($prm['application'], $config);
        $cda->init();
        
              
        $mod= Mod::get();
        
        $mod->begin();
        
        $res= $cda->initMeta();
        $this->assertEquals([cda::CODE=>$codeName,'test'=>$testName], $res);
         
        $res= $cda->initData();
        $this->assertEquals([1], $res);
        
        $mod->end();
    }
}
