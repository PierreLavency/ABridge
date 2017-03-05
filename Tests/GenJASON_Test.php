<?php
	
require_once('Logger.php');
require_once('GenJason.php');
require_once('GenJason_case.php');

class GenJASON_Test extends PHPUnit_Framework_TestCase {

	protected static $log;
	protected static $db;

	public static function setUpBeforeClass()
	{		
		self::$log=new Logger('GenJASON_init');
		self::$log->load();
		$db = getBaseHandler('dataBase','test');
		$db->setLogLevl(0);
		self::$db=$db;
		initStateHandler('TestDir', 'dataBase','test');
		initStateHandler('TestFle', 'dataBase','test');	
	}
	

	public function testFormElmOut()
    {
		$test=GenJASONCases();
		
		self::$db->beginTrans();
		
		$h= new Model($test[0][0],$test[0][1]);
		
		$this->expectOutputString(self::$log->getLine(0));
		$this->assertNotNull(genJason($h,true,false,$test[0][2]));
		
		self::$db->commit();
    }

	
    /**
     * @dataProvider Provider1
     */
 
	public function testJason($a,$b,$c,$expected)
    {	
	
		self::$db->beginTrans();
		
		$h= new Model($a,$b);

        $this->assertEquals(self::$log->getLine($expected),genJASON($h,false,false,$c));

		self::$db->commit();
    }
 
    public function Provider1() {
        return GenJASONCases();
    }

}
?>	