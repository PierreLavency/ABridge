<?php
	
require_once('Logger.php');
require_once('View_case.php');

class View_Test extends PHPUnit_Framework_TestCase {

	protected static $log;

	public static function setUpBeforeClass()
	{		
		self::$log=new Logger('View_init');
		self::$log->load();
	}
	
	
	public function testViewOut()
    {
		$test= viewCases();
		$v = $test[0][0];
		$p = $test[0][1];
		$s = $test[0][2];
		$e = self::$log->getLine(0);
		$this->expectOutputString(self::$log->getLine(0));
		$this->assertNotNull($v->show($p,$s,true));
    }
	
	
    /**
     * @dataProvider Provider1
     */
 
	public function testView($v,$p,$s,$expected)
    {
		
        $this->assertEquals(self::$log->getLine($expected),$v->show($p,$s,false));
    }
 
    public function Provider1() {
        return viewCases();
    }

}
