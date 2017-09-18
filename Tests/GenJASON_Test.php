<?php
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Log\Logger;
use ABridge\ABridge\GenJason;

require_once('GenJason_case.php');

class GenJASON_Test extends PHPUnit_Framework_TestCase
{

    protected static $log;
    protected static $db;
    protected static $prm;
    
    public static function setUpBeforeClass()
    {
        $classes = ['testDir','testFile','CodeVal','Code'];
        $baseTypes=['dataBase'];
        
        $prm=UtilsC::genPrm($classes, 'GENJASON_Test', $baseTypes);
        
        Mod::get()->reset();
        Mod::get()->init($prm['application'], $prm['handlers']);
               
        self::$log=new Logger();
        self::$log->load('C:/Users/pierr/ABridge/Datastore/', 'GenJASON_init');
    }
    

    public function testFormElmOut()
    {
        $test=GenJASONCases();
        
        Mod::get()->begin();
        
        $h= new Model($test[0][0], $test[0][1]);
        
        $this->expectOutputString(self::$log->getLine(0));
        $this->assertNotNull(GenJASON::genJASON($h, true, false, $test[0][2]));
        
        Mod::get()->End();
    }

    
    /**
     * @dataProvider Provider1
     */
 
    public function testJason($a, $b, $c, $expected)
    {

    
        Mod::get()->begin();
        
        $h= new Model($a, $b);

        $this->assertEquals(self::$log->getLine($expected), GenJASON::genJASON($h, false, false, $c));

        Mod::get()->End();
    }
 
    public function Provider1()
    {
        return GenJASONCases();
    }
}
