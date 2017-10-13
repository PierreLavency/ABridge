<?php

use ABridge\ABridge\Apps\Cda;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class Cda_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        $classes = [Cda::CODE,'test'];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        
        $testName=$prm['fileBase']['test'];
        $codeName=$prm['fileBase'][cda::CODE];
        $config = [
                cda::CODE=>$codeName,
                Cda::CODELIST=>['test'=>$testName],
                Cda::CODEDATA=>[$testName=>['x']],
                
        ];
        
        $res = Cda::init($prm, $config);
        
        $this->assertEquals([], $res['Handlers'][$testName]);

        Mod::reset();
               
        $mod= Mod::get();
        
        $mod->init($prm['application'], $res['Handlers']);
        
        $mod->begin();
        
        $res= cda::initMeta($config);
        $this->assertEquals($prm['fileBase'], $res);
  
        
        $res= cda::initData($config);
        $this->assertEquals(1, $res);
        
        $mod->end();
    }
}
