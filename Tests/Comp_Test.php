<?php
    
use ABridge\ABridge\Comp;

class Comp_Test extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider Provider1
     */
 
    public function testnorm($a, $expected)
    {
        $this->assertEquals($expected, Comp::normBindings($a));
    }
 
    public function Provider1()
    {
        return [
        	[['X'],['X'=>'X']],
        	[[],[]],
        	[['X'=>'X'],['X'=>'X']],
            ];
    }
}
