<?php
    
use ABridge\ABridge\Logger;
use ABridge\ABridge\Handler;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\GenJASON;

require_once('GenJason_case.php');

class GenJASON_Test extends PHPUnit_Framework_TestCase
{

    protected static $log;
    protected static $db;

    public static function setUpBeforeClass()
    {
        $prm=[
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];
        self::$log=new Logger('GenJASON_init');
        self::$log->load();
        $db =Handler::get()->setBase('dataBase', 'test', $prm);
        $db->setLogLevl(0);
        self::$db=$db;
        Handler::get()->setStateHandler('TestDir', 'dataBase', 'test');
        Handler::get()->setStateHandler('TestFle', 'dataBase', 'test');
    }
    

    public function testFormElmOut()
    {
        $test=GenJASONCases();
        
        self::$db->beginTrans();
        
        $h= new Model($test[0][0], $test[0][1]);
        
        $this->expectOutputString(self::$log->getLine(0));
        $this->assertNotNull(GenJASON::genJASON($h, true, false, $test[0][2]));
        
        self::$db->commit();
    }

    
    /**
     * @dataProvider Provider1
     */
 
    public function testJason($a, $b, $c, $expected)
    {
        self::$db->connect();
    
        self::$db->beginTrans();
        
        $h= new Model($a, $b);

        $this->assertEquals(self::$log->getLine($expected), GenJASON::genJASON($h, false, false, $c));

        self::$db->commit();
    }
 
    public function Provider1()
    {
        return GenJASONCases();
    }
}
