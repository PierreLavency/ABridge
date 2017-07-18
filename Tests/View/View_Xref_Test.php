<?php
    
use ABridge\ABridge\Logger;
use ABridge\ABridge\Handler;

use ABridge\ABridge\Hdl\Request;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\View\View;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

require_once 'View_case_Xref.php';

class View_Xref_Test extends PHPUnit_Framework_TestCase
{

    protected static $log;
    protected static $db;
    public static function setUpBeforeClass()
    {
        self::$log=new Logger('View_init_Xref');
        self::$log->load();
        self::$db = Handler::get()->getBase('dataBase', 'test');
        Handler::get()->setStateHandler('dir', 'dataBase', 'test');
    }
    
    
    public function testViewOut()
    {
        $test= viewCasesXref();
        $id = $test[0][0];
        $p = $test[0][1];
        $s = $test[0][2];
        self::$db->beginTrans();

        $request = new Request($p, CstMode::V_S_READ);
        $handle = new Handle($p, CstMode::V_S_READ, null);
        $v = new View($handle);
        $v->setTopMenu(['/dir']);
        $v->setAttrList(['Name'], CstView::V_S_REF);
        $v->setAttrListHtml(['Mother'=>CstHTML::H_T_SELECT], CstMode::V_S_CREA);
        
        $this->expectOutputString(self::$log->getLine(0));
        $this->assertNotNull($v->show($s, true));
        self::$db->commit();
    }
    

    /**
     * @dataProvider Provider1
     */
 
    public function testView($id, $p, $s, $expected)
    {

        self::$db->beginTrans();
    
        $home= null;
        $request = new Request($p, CstMode::V_S_READ);
        $handle = new Handle($p, CstMode::V_S_READ, $home);
        $v = new View($handle);
    
        $v->setTopMenu(['/dir']);
        $v->setAttrList(['Name'], CstView::V_S_REF);
        $v->setAttrListHtml(['Mother'=>CstHTML::H_T_SELECT], CstMode::V_S_CREA);
    
        $this->assertEquals(self::$log->getLine($expected), $v->show($s, false));
        self::$db->commit();
    }
 
    public function Provider1()
    {
        return viewCasesXref();
    }
}
