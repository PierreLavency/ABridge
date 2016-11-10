<?php
	

require_once('Logger.php');


class Logger_Test extends PHPUnit_Framework_TestCase {

 
	public function testLogLine1()
    {
		$logName = basename(__FILE__, ".php");
		
		$this->assertNotNull(($x = new Logger($logName)));
		
		$this->assertEquals(0,$x->logLine ("this is my first logged line"));
					
		$this->assertEquals(1,$x->logLine ("this is my second logged line"));

		$this->assertNotNull ($x->save());	
    }
 
	/**
     * @depends testLogLine1
     */
	
	public function testLogLine2()
    {
		$logName = basename(__FILE__, ".php");
		
		$this->assertNotNull(($x = new Logger($logName)));
		
		$this->assertEquals(0,$x->logLine ("this is my first again logged line"));
					
		$this->assertNotNull ($x->save());	
    }
 
 
 	/**
     * @depends testLogLine2
     */
 
	public function testLogLine3()
    {
		$logName = basename(__FILE__, ".php");
		
		$this->assertNotNull(($x = new Logger($logName)));
		
		$this->assertTrue($x->load());
		
		$this->assertEquals(1,$x->logSize());
					
    }
}

?>	