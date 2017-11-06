<?php
    
use ABridge\ABridge\Log\Logger;

use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\View\View;
use ABridge\ABridge\View\Vew;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

require_once 'View_case_Xref.php';

class View_Xref_Test extends PHPUnit_Framework_TestCase
{

    protected static $log;
    protected static $runLog;
    protected static $db;
    public static function setUpBeforeClass()
    {
        $classes = ['Dir'];
        $baseTypes=['dataBase'];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        Mod::reset();
        Mod::get()->init($prm['application'], $prm['handlers']);
        Vew::reset();
       
        self::$log=new Logger();
        self::$log->load('C:/Users/pierr/ABridge/Datastore/', 'View_init_Xref');
    }
    
    
    public function testViewOut()
    {
        $test= viewCasesXref();
        $id = $test[0][0];
        $p = $test[0][1];
        $s = $test[0][2];
        
        Mod::get()->begin();

        $handle = new Handle($p, CstMode::V_S_READ, null);
        $cname=$handle->getModName();

        Vew::reset();
        $cname=$handle->getModName();
        $config = [
                $cname=> [
                        'attrList' => [
                                CstView::V_S_REF        => ['Name'],
                                
                        ],
                        'attrHtml'=> [
                                CstMode::V_S_CREA => [
                                        'Mother'=>CstHTML::H_T_SELECT,
                                ]
                        ],
                ]
                
        ];
        Vew::get()->init(['name'=>"test"], $config);
        
        $v = new View($handle);
        $v->setTopMenu(['/dir']);
        
        $this->expectOutputString(self::$log->getLine(0));
        $this->assertNotNull($v->show($s, true));
        
        Mod::get()->end();
    }
    

    /**
     * @dataProvider Provider1
     */
 
    public function testView($id, $p, $s, $expected)
    {

        Mod::get()->begin();
    
        $handle = new Handle($p, CstMode::V_S_READ, null);
        Vew::reset();
        $cname=$handle->getModName();
        $config = [
                $cname=> [
                        'attrList' => [
                                CstView::V_S_REF        => ['Name'],
                                
                        ],
                        'attrHtml'=> [
                                CstMode::V_S_CREA => [
                                        'Mother'=>CstHTML::H_T_SELECT,
                                ]
                        ],
                ]
                
        ];
        Vew::get()->init(['name'=>"test"], $config);
        $v = new View($handle);
        $v->setTopMenu(['/dir']);
        $res = $v->show($s, false);
        
        $this->assertEquals(self::$log->getLine($expected), $res);
        Mod::get()->end();
    }
 
    public function Provider1()
    {
        return viewCasesXref();
    }
}
