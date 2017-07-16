<?php
    
use ABridge\ABridge\Handler;

class Handler_Test extends PHPUnit_Framework_TestCase
{

 
    public function testBaseHandler1()
    {
        $this->assertTrue(Handler::get()->resetHandlers());
        $this->assertNotNull($db1 = Handler::get()->getBase('dataBase', 'test'));
        $this->assertNotNull($db2 = Handler::get()->getBase('dataBase', 'test'));
        $this->assertEquals($db1, $db2);
    }
    
    public function testBaseHandler2()
    {
        $this->assertTrue(Handler::get()->resetHandlers());
        $this->assertNotNull($db1 = Handler::get()->getBase('dataBase', 'test'));
        $this->assertNotNull($db2 = Handler::get()->getBase('fileBase', 'test'));
        $this->assertNotEquals($db1, $db2);
    }
    
    public function testBaseHandler3()
    {
        $this->assertTrue(Handler::get()->resetHandlers());
        $this->assertNotNull($db1 = Handler::get()->getBase('fileBase', 'test'));
        $this->assertNotNull($db2 = Handler::get()->getBase('fileBase', 'test1'));
        $this->assertFalse($db2 = Handler::get()->getBase('NOTEXISTS', 'NOTEXISTS'));
        $this->assertNotEquals($db1, $db2);
    }
    
    public function testStateHandler()
    {
        $this->assertTrue(Handler::get()->resetHandlers());
        $this->assertNotNull($db = Handler::get()->setStateHandler('CLass', 'fileBase', 'test'));
        $this->assertNotNull($db = Handler::get()->setStateHandler('CLass', 'fileBase', 'test'));
        $this->assertNotNull($c1 = Handler::get()->getStateHandler('CLass'));
        $this->assertNotNull($c2 = Handler::get()->getStateHandler('CLass'));
        $this->assertEquals($c1, $c2);
    }
 
    public function testViewHandler()
    {
        $this->assertTrue(Handler::get()->resetHandlers());
        $x = 'x';
        Handler::get()->setViewHandler($x, $x);
        $y = Handler::get()->getViewHandler($x);
        $this->assertEquals($x, $y);
        $y = Handler::get()->getViewHandler('yy');
        $this->assertNull($y);
    }
}
