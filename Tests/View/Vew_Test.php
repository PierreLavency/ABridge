<?php
    
use ABridge\ABridge\View\Vew;

class Vew_Test extends PHPUnit_Framework_TestCase
{
    public function testViewHandler()
    {
        $this->assertTrue(Vew::reset());
        $x = ['x'=>'xx','y'=>'y'];
        Vew::get()->init([], $x);
        $y = Vew::get()->getViewPrm('x');
        $this->assertEquals('xx', $y);
        $y = Vew::get()->getViewPrm('yy');
        $this->assertNull($y);
        
        $this->assertEquals([], Vew::get()->initMeta());
        $this->assertFalse(Vew::get()->isNew());
    }
}
