<?php
	

require_once('Type.php');


class TypeTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider Provider1
     */
 
	public function testcheckType($a, $b, $expected)
    {
        $this->assertEquals($expected,checkType($a,$b));
    }
 
    public function Provider1() {
        return [
            ['1', 	M_INT,		0],
            [1, 	M_INT,		1],
            [1.5, 	M_INT,		0],
            ['1', 	M_FLOAT,	0],
	        [1, 	M_FLOAT,	0],
            [1.5, 	M_FLOAT,	1],		
            ['1', 	M_STRING,	1],
	        [1, 	M_STRING, 	0],
            [1.5, 	M_STRING, 	0],		
			[0, 	M_BOOL,		0],		
            ['x', 	M_BOOL,		0],
	        [false,	M_BOOL,		1],
            [1, 	M_BOOL, 	0],		
			[-1, 	M_INTP,		0],		
            [1, 	M_INTP,		1],
	        [0	,	M_INTP,		1],
            ['1', 	M_INTP, 	0],		
			[1, 	M_ALNUM,	0],		
            ['1', 	M_ALNUM,	1],
	        ['A1',	M_ALNUM,	1],
			[1, 	M_ALPHA,	0],		
            ['1', 	M_ALPHA,	0],
	        ['A1',	M_ALPHA,	0],
	        ['Abb',	M_ALPHA,	1],
			[-1,						M_DATE,		0],
			['25-10-2016',				M_DATE,		1],
			[' 25-10-2016 12:30:48',	M_DATE,		0],		
			['26-05-1959'			,	M_DATE,		1],		
			['26-5-1959'			,	M_DATE,		0],		
			['31-02-1959'			,	M_DATE,		0],		
			['28-02-2016'			,	M_DATE,		1],		
			['29-02-2016'			,	M_DATE,		1],		
			['25-10-2016 12:30:48'	,	M_TMSTP,	1],				
			['25-10-2016'			,	M_TMSTP,	0],				
			['now'					,	M_TMSTP,	0],				
			];
    }
	/**
     * @dataProvider Provider2
     */
 
	public function testisMtype($a, $expected)
    {
        $this->assertEquals($expected,isMtype($a));
    }
	
	   public function Provider2(){
        return [
            [M_INT,		1],
            ['nint',	0],
			];
    }
 
	/**
     * @dataProvider Provider3
     */
 
	public function testconvertString($X,$Type,$expected)
    {
        $this->assertEquals($expected,convertString($X,$Type));
    }
	
   public function Provider3() {
        return [
            ['1', 		M_INT,		1],
            ['1', 		M_CODE,		1],
            [ "true", 	M_BOOL,		true],
            ['1.5', 	M_FLOAT,	1.5],
	        ["pp", 		M_INT,		"pp"],	
 		
			];
    }	
}

?>	