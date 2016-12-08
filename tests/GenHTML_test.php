<?php
	
require_once('Logger.php');
require_once('GenHTML.php');
require_once('GenHTML_case.php');

class GenHTML_Test extends PHPUnit_Framework_TestCase {

	protected static $log;

	public static function setUpBeforeClass()
	{		
		self::$log=new Logger('GenHTML_init');
		self::$log->load();
	}
	
	
	public function testFormElmOut()
    {
		global $test;
		$this->expectOutputString(self::$log->getLine(0));
		$this->assertNotNull(genFormElem($test[0][0],true));
    }
	
	
    /**
     * @dataProvider Provider1
     */
 
	public function testFormElm($a,$expected)
    {
        $this->assertEquals(self::$log->getLine($expected),genFormElem($a,false));
    }
 
    public function Provider1() {
		global $test;
        return $test;
    }

}
?>	