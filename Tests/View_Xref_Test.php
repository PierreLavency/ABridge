<?php
	
require_once('Logger.php');
require_once('View_case_Xref.php');

class View_Xref_Test extends PHPUnit_Framework_TestCase {

	protected static $log;
	protected static $db;
	public static function setUpBeforeClass()
	{		
		self::$log=new Logger('View_init_Xref');
		self::$log->load();
		self::$db = getBaseHandler('dataBase','test');
		initStateHandler('dir', 'dataBase','test');
	}
	
	
	public function testViewOut()
    {
		$test= viewCasesXref();
		$id = $test[0][0];
		$p = $test[0][1];
		$s = $test[0][2];	
		self::$db->beginTrans();
		$y =  new Model('dir',$id);
		$path = new Path($p);
		$v = new View($y);
		$v->setNavClass(['dir']);	
		$v->setAttrList(['Name'],V_S_REF);	
		$v->setAttrListHtml(['Mother'=>H_T_SELECT], V_S_CREA);		
		
		$this->expectOutputString(self::$log->getLine(0));
		$this->assertNotNull($v->show($path,$s,true));
		self::$db->commit();
    }
	

    /**
     * @dataProvider Provider1
     */
 
	public function testView($id,$p,$s,$expected)
    {

	self::$db->beginTrans();
	$y =  new Model('dir',$id);
	$path = new Path($p);
	$v = new View($y);
	$v->setNavClass(['dir']);	
	$v->setAttrList(['Name'],V_S_REF);	
	$v->setAttrListHtml(['Mother'=>H_T_SELECT], V_S_CREA);
	
    $this->assertEquals(self::$log->getLine($expected),$v->show($path,$s,false));
	self::$db->commit();
    }
 
    public function Provider1() {
        return viewCasesXref();
    }

}
