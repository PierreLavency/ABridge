<?php
    

require_once 'Handler.php';


class Handler_Test extends PHPUnit_Framework_TestCase
{

 
    public function testBaseHandler1()
    {
        $this->assertTrue(resetHandlers());
        $this->assertNotNull($db1 = getBaseHandler('dataBase', 'test'));
        $this->assertNotNull($db2 = getBaseHandler('dataBase', 'test'));
        $this->assertEquals($db1, $db2);
    }
    
    public function testBaseHandler2()
    {
        $this->assertTrue(resetHandlers());
        $this->assertNotNull($db1 = getBaseHandler('dataBase', 'test'));
        $this->assertNotNull($db2 = getBaseHandler('fileBase', 'test'));
        $this->assertNotEquals($db1, $db2);
    }
    
    public function testBaseHandler3()
    {
        $this->assertTrue(resetHandlers());
        $this->assertNotNull($db1 = getBaseHandler('fileBase', 'test'));
        $this->assertNotNull($db2 = getBaseHandler('fileBase', 'test1'));
        $this->assertFalse($db2 = getBaseHandler('NOTEXISTS', 'NOTEXISTS'));
        $this->assertNotEquals($db1, $db2);
    }
    
    public function testStateHandler()
    {
        $this->assertTrue(resetHandlers());
        $this->assertNotNull($db = initStateHandler('CLass', 'fileBase', 'test'));
        $this->assertNotNull($db = initStateHandler('CLass', 'fileBase', 'test'));
        $this->assertNotNull($c1 = getStateHandler('CLass'));
        $this->assertNotNull($c2 = getStateHandler('CLass'));
        $this->assertEquals($c1, $c2);
    }
 
    public function testViewHandler()
    {
        $this->assertTrue(resetHandlers());
        $x = 'x';
        Handler::get()->setViewHandler($x, $x);
        $y = Handler::get()->getViewHandler($x);
        $this->assertEquals($x, $y);
        $y = Handler::get()->getViewHandler('yy');
        $this->assertNull($y);
    }
}
