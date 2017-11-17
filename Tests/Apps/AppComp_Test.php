<?php
use ABridge\ABridge\Apps\Cdv;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\AppComp;

class AdmComp_Test_App extends AppComp
{
    public function __construct($prm, $bindings)
    {
        $this->bindings=$bindings;
        $test = $this->bindings['test'];
        
        $this->config= [
                'Handlers' => [
                        $test => [],
                ],
                
        ];
    }
    
    public function initOwnMeta($prm)
    {
        return $this->bindings;
    }
    
    public function initOwnData($prm)
    {
        return ['test'];
    }
}

class AdmComp_Test_config extends AppComp
{

}

class AppComp_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        Mod::reset();
        $classes = [Cdv::CODE,Cdv::CODEVAL];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        $codeName=$prm['fileBase'][cdv::CODE];
        $codeVal=$prm['fileBase'][cdv::CODEVAL];

        $cconfig = [
                'Default' => [
                        'base' =>'fileBase'
                ],
                'Apps' => [
                        'cdv' => [
                                cdv::CODE=>$codeName,
                                cdv::CODEVAL=>$codeVal,
                                Cdv::CODELIST=>['test'],
                                Cdv::CODEDATA=>['test'=>['x']],
                                
                        ],
                ],
                'Log' => [],
                
        ];
                    
        $config = new AdmComp_Test_config([], []);
        $config->setConfig($cconfig);
        $config->setPrm($prm['application']);

        
        $config->init();
                    
        $mod= Mod::get();
        
        $mod->begin();
        
        $res= $config->initMeta();
        $this->assertEquals($prm['fileBase'][cdv::CODE].'/1', $res['test']);
         
        $res= $config->initData();
        $this->assertEquals([1], $res);
        
        $mod->end();
    }
}
