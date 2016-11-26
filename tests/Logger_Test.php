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
		
		$this->assertFalse($x->getLine(10000));
		
		$this->assertNotNull($l=$x->getLine(0));
		
		$this->assertEquals($l,"this is my first again logged line");
		
		$this->assertFalse($x->showLine(10000,false));
		
		$this->assertNotNull($ls=$x->showLine(0,false));
		
		$r= "LINE:0<br>".$l."<br>";
		
		$this->assertEquals($ls,$r);
		
		$o=$r;
		$this->expectOutputString($o);
		$this->assertNotNull($ls=$x->showLine(0));
		
		$r = '<br>'.$r. '<br>';
		
		$this->assertEquals($x->show(false),$r);
		
		$o=$o.$r;
		$this->expectOutputString($o);
		$this->assertEquals($x->show(),$r);
				
				
		$this->assertNotNull(($y = new Logger($logName)));
		
		$this->assertTrue($y->load());
		
		$this->assertFalse($x->diff($y));
		
		$this->assertEquals($x->show(false),$r);
		
		$this->assertEquals(2, $x->includeLog($y));
		
		$this->assertNotNull(($z = new Logger($logName)));
		
		$this->assertEquals($z->show(false),"");
		$this->expectOutputString($o);
		$this->assertEquals($z->show(),"");
				
		$this->assertEquals(0,$z->logLine ("this is my first again and again logged line"));
		
		$this->assertEquals($y->diff($z),1);
		$this->assertEquals($x->diff($z),-1);
    }
}

?>	