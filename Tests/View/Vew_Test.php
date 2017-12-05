<?php
    
use ABridge\ABridge\View\Vew;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\Hdl\CstMode;

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
                'Home' => ['/'],
                'MenuExcl'=> ['/'],
                'specName1' => [
                        CstMode::V_S_READ => [
                                'prm' => 'val1'
                        ],
                        
                ],
                'navList'=> [
                        CstMode::V_S_READ => ['val2'],
                        
                ],
                'specName3' => 'val3',
                'lblList'=> ['prm'=>'val4'],
                'modLblList'=>['mod'=>'modLbl'],
                'modName' => [
                        'topList' => 'val32',
                        'listHtmlClassElem'=> ['prm'=>'val42'],
                        'specName12' => [
                                CstMode::V_S_READ => [
                                        'prm' => 'val12'
                                ]
                        ],
                        'specName22' => [
                                CstMode::V_S_READ => 'val22',
                        ],
                        'viewList' => [
                                'viewName1' => [
                                        'listHtml'=> [
                                                CstMode::V_S_READ => [
                                                        'prm' => 'val13'
                                                ]
                                        ],
                                        'attrProp'=> [
                                                CstMode::V_S_READ => 'val23',
                                                
                                        ],
                                        'beforeViews' => 'val33',
                                        'listHtmlClass'=> ['prm'=>'val43'],
                                ],

                        ]
                ]
        ];
        
        
        $this->assertTrue(Vew::reset());
        $vew= Vew::get();
        $vew->init(['name'=>'testVew','cssName'=>'testVewCss','fpath'=>"C:\Users\pierr\ABridge\Tests\View\\"], $config);
        
        $this->assertEquals('testVew', $vew->getAppName());
        $this->assertEquals('test', $vew->getCssFileName());
        $this->assertEquals(['/'], $vew->getHome());
        $this->assertEquals(['/'], $vew->getExcl());
        $this->assertNotNull($vew->getTopMenu(CstMode::V_S_READ, []));
        
        $this->assertNull($vew->getViewPrm('nospec'));
        $this->assertNotNull($vew->getCredit());
        $this->assertEquals('val3', $vew->getViewPrm('specName3'));

        $this->assertEquals('val13', $vew->getHtmlList('modName', 'viewName1', CstMode::V_S_READ, 'prm'));
        $this->assertEquals('val12', $vew->getSpecStateAttr('modName', 'viewName1', CstMode::V_S_READ, 'specName12', 'prm'));
        $this->assertEquals('val1', $vew->getSpecStateAttr('modName', 'viewName1', CstMode::V_S_READ, 'specName1', 'prm'));

        $this->assertEquals('val23', $vew->getPropList('modName', 'viewName1', CstMode::V_S_READ));
        $this->assertNotNull($vew->getPropList('NotExist', 'viewName1', CstMode::V_S_READ));
        $this->assertEquals('val22', $vew->getSpecState('modName', 'viewName1', CstMode::V_S_READ, 'specName22'));
        $this->assertNotNull($vew->getMenuObjAction('modName', 'viewName1', CstMode::V_S_READ));
        
        $this->assertEquals('val33', $vew->getRelViews('modName', 'viewName1', 'before'));
        $this->assertEquals('val32', $vew->getTopLists('modName', 'viewName1'));
        $this->assertEquals('val3', $vew->getSpec('modName', 'viewName1', 'specName3'));
        
 
        $this->assertEquals('val43', $vew->getHtmlClassList('modName', 'viewName1', 'prm'));
        $this->assertEquals('val42', $vew->getHtmlClassListElem('modName', 'viewName1', 'prm'));
        $this->assertEquals('val4', $vew->getLbl('modName', 'viewName1', 'prm'));
        $this->assertEquals('modLbl', $vew->getModLbl('mod'));
        
        $this->assertEquals(['viewName1'], $vew->getViewList('modName', CstMode::V_S_READ));
        $this->assertNotNull($vew-> getMenuObjView('modName', CstMode::V_S_READ));
        
        $this->assertEquals('viewName1', $vew->getDefViewName('modName', CstMode::V_S_READ));
        
        $vew->init([], $config2);
        $this->assertEquals(['viewName1','viewName2'], $vew->getViewList('modName', CstMode::V_S_READ));
        
        $vew->init([], $config3);
        $this->assertEquals([], $vew->getViewList('modName2', CstMode::V_S_READ));
        
        $this->assertEquals([], $vew->initMeta());
        $this->assertFalse($vew->isNew());
    }
}
