<?php
    

require_once 'CstError.php';


class CstError_Test extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider Provider1
     */
 
    public function testsubst($a, $expected)
    {
        $this->assertEquals($expected, CstError::subst($a));
    }
 
    public function Provider1()
    {
        return [
            [E_ERC001.':X','Illegal operation on predefined attribute'.':'.E_ERC001.':X'],
            [':X',':X'],         
			['y:X','y:X'],
			['yX','yX'],
            ];
    }

  
    
    
 
}
