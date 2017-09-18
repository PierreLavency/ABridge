<?php
    
use ABridge\ABridge\Log\Logger;

use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\View\View;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

require_once 'View_case_Xref.php';

class View_Xref_Test extends PHPUnit_Framework_TestCase
{

    protected static $log;
    protected static $db;
    public static function setUpBeforeClass()
    {
        $classes = ['Dir'];
        $baseTypes=['dataBase'];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        Mod::reset();
        Mod::get()->init($prm['application'], $prm['handlers']);
        
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

        $request = new Request($p, CstMode::V_S_READ);
        $handle = new Handle($p, CstMode::V_S_READ, null);
        $v = new View($handle);
        $v->setTopMenu(['/dir']);
        $v->setAttrList(['Name'], CstView::V_S_REF);
        $v->setAttrListHtml(['Mother'=>CstHTML::H_T_SELECT], CstMode::V_S_CREA);
        
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
    
        $home= null;
        $request = new Request($p, CstMode::V_S_READ);
        $handle = new Handle($p, CstMode::V_S_READ, $home);
        $v = new View($handle);
    
        $v->setTopMenu(['/dir']);
        $v->setAttrList(['Name'], CstView::V_S_REF);
        $v->setAttrListHtml(['Mother'=>CstHTML::H_T_SELECT], CstMode::V_S_CREA);
    
        $this->assertEquals(self::$log->getLine($expected), $v->show($s, false));
        Mod::get()->end();
    }
 
    public function Provider1()
    {
        return viewCasesXref();
    }
}
