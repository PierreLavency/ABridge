<?php
    
use ABridge\ABridge\View\Vew;

class Vew_Test extends PHPUnit_Framework_TestCase
{
        
    
    
    public function testViewHandler()
    {
        $config3 = [
                'modName2' => [
                    'viewListMenu'=> [],
                ]
        ];
        
        $config2 = [
                'modName' => [
                        'viewList' => [
                                'viewName2' => []
                                ]
                        ]
        ];
        
        $config = [
                'specName1' => [
                        'state' => [
                                'prm' => 'val1'
                        ],
                        
                ],
                'specName2' => [
                        'state' => 'val2',
                        
                ],
                'specName3' => 'val3',
                        
                'modName' => [
                        'specName32' => 'val32',
                        'specName12' => [
                                'state' => [
                                        'prm' => 'val12'
                                ]
                        ],
                        'specName22' => [
                                'state' => 'val22',
                        ],
                        'viewList' => [
                                'viewName1' => [
                                        'specName13' => [
                                                'state' => [
                                                        'prm' => 'val13'
                                                ]
                                        ],
                                        'specName23' => [
                                                'state' => 'val23',
                                                
                                        ],
                                        'specName33' => 'val33',
                                ],

                        ]
                ]
        ];
        
        
        $this->assertTrue(Vew::reset());
        $vew= Vew::get();
        $vew->init(['name'=>'testVew','cssName'=>'testVewCss','fpath'=>"C:\Users\pierr\ABridge\Tests\View\\"], $config);
        
        $this->assertEquals('testVew', $vew->getAppName());
        $this->assertEquals('test', $vew->getCssFileName());
        
        $this->assertNull($vew->getViewPrm('nospec'));
        $this->assertEquals('val3', $vew->getViewPrm('specName3'));

        $this->assertEquals('val13', $vew->getSpecStateAttr('modName', 'viewName1', 'state', 'specName13', 'prm'));
        $this->assertEquals('val12', $vew->getSpecStateAttr('modName', 'viewName1', 'state', 'specName12', 'prm'));
        $this->assertEquals('val1', $vew->getSpecStateAttr('modName', 'viewName1', 'state', 'specName1', 'prm'));

        $this->assertEquals('val23', $vew->getSpecState('modName', 'viewName1', 'state', 'specName23'));
        $this->assertEquals('val22', $vew->getSpecState('modName', 'viewName1', 'state', 'specName22'));
        $this->assertEquals('val2', $vew->getSpecState('modName', 'viewName1', 'state', 'specName2'));
        
        $this->assertEquals('val33', $vew->getSpec('modName', 'viewName1', 'specName33'));
        $this->assertEquals('val32', $vew->getSpec('modName', 'viewName1', 'specName32'));
        $this->assertEquals('val3', $vew->getSpec('modName', 'viewName1', 'specName3'));
        
        $this->assertEquals(['viewName1'], $vew->getViewList('modName', 'state'));
        $this->assertEquals('viewName1', $vew->getDefViewName('modName', 'state'));
        
        $vew->init([], $config2);
        $this->assertEquals(['viewName1','viewName2'], $vew->getViewList('modName', 'state'));
        
        $vew->init([], $config3);
        $this->assertEquals([], $vew->getViewList('modName2', 'state'));
        
        $this->assertEquals([], $vew->initMeta());
        $this->assertFalse($vew->isNew());
    }
}
