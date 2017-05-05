<?php
	

require_once('Type.php');


class Type_Test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider Provider1
     */
 
	public function testcheckType($a, $b, $expected)
    {
        $this->assertEquals($expected,checkType($a,$b));
    }
 
    public function Provider1() {
        return [
			['x', 	'not',		0],
			[null, 	M_INT,		1],
			['1', 	M_INT,		0],
            [1, 	M_INT,		1],
            [1.5, 	M_INT,		0],
            [null, 	M_FLOAT,	1],           
			['1', 	M_FLOAT,	0],	
	        [1, 	M_FLOAT,	0],
            [1.5, 	M_FLOAT,	1],		
            [null, 	M_STRING,	1],
            ['1', 	M_STRING,	1],
	        [1, 	M_STRING, 	0],
            [1.5, 	M_STRING, 	0],		
			[0, 	M_BOOL,		0],		
            ['x', 	M_BOOL,		0],
	        [false,	M_BOOL,		1],
            [1, 	M_BOOL, 	0],		
			[-1, 	M_INTP,		0],		
            [1, 	M_INTP,		1],
	        [0	,	M_INTP,		0],
            ['1', 	M_INTP, 	0],		
			[null, 	M_ALNUM,	1],	
			[1, 	M_ALNUM,	0],		
            ['1', 	M_ALNUM,	1],
	        ['A1',	M_ALNUM,	1],
			[1, 	M_ALPHA,	0],		
            ['1', 	M_ALPHA,	0],
	        ['A1',	M_ALPHA,	0],
	        ['Abb',	M_ALPHA,	1],
			[null,						M_DATE,		1],			
			[-1,						M_DATE,		0],
			['2016-10-25',				M_DATE,		1],
			[' 2016-10-25 12:30:48',	M_DATE,		0],		
			['1959-05-26'			,	M_DATE,		1],		
			['1959-5-59'			,	M_DATE,		0],		
			['1959-02-31'			,	M_DATE,		0],		
			['2016-02-28'			,	M_DATE,		1],		
			['2016-02-29'			,	M_DATE,		1],		
			['2016-10-25 12:30:48'	,	M_TMSTP,	1],				
			['25-10-2016'			,	M_TMSTP,	0],				
			['now'					,	M_TMSTP,	0],			
			['now          vvvvvv'	,	M_TXT,		1],
			['now  </br>   vvvvvv'	,	M_TXT,		0],			
			['now  </br>   vvvvvv'	,	M_RTXT,		1],	
			['["x","y",{"x":"y<>z"}]'	,	M_JSON,		1],	
			['["x","y",{"x":"y<>z"},]'	,	M_JSON,		0],						
			];
    }

	/**
     * @dataProvider Provider_isStruct
     */
 
	public function testisStruc($a, $expected, $e2)
    {
        $this->assertEquals($expected,isStruct($a));
		$this->assertEquals($e2,isRaw($a));
    }
	
	   public function Provider_isStruct(){
        return [
            [M_INT,		true, 	false],
            [M_JSON,	false,	true]
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
		    ['', 		M_INT,		null],
            ['1', 		M_INT,		1],
            ['1', 		M_CODE,		1],
            ['', 		M_CODE,		null],
            [ "true", 	M_BOOL,		true],
            [ "false", 	M_BOOL,		false],
            ['1.5', 	M_FLOAT,	1.5],
            ['x', 	    M_FLOAT,	'x'],
			['x', 	    M_BOOL,	'x'],
	        ["pp", 		M_INT,		"pp"],	
 			["pp", 		M_TXT,		"pp"],
 			["pp", 		M_RTXT,		"pp"],				
			];
    }	
	
	
	/**
     * @dataProvider Provider4
     */
	function testconvertSqlType($x,$expected) {
		$this->assertEquals($expected,convertSqlType($x));
	}
	
	public function Provider4() {
        return [
            [M_INT,		'INT(11)'],
			[M_TMSTP,	'TIMESTAMP'],
            [M_INTP, 	'INT(11) UNSIGNED'],
            [M_STRING, 	'VARCHAR(255)'],
            [M_FLOAT,	'FLOAT'],
			[M_CREF, 	'INT(11) UNSIGNED'],
			[M_BOOL, 	'BOOLEAN'],
			[M_ALNUM, 	'VARCHAR(255)'],
			[M_ALPHA, 	'VARCHAR(255)'],
			[M_DATE, 	'DATE'],
			[M_TXT, 	'TEXT'],
			[M_RTXT, 	'TEXT'],		
			['notexists', 	false],
			];
    }	
	
	/**
     * @dataProvider Provider5
     */
	function testidentif($x,$expected) {
		$this->assertEquals($expected,checkIdentifier($x));
	}

	public function Provider5() {
        return [
			['1',	false],
			['_',	true ],
			['A',	true ],
			['A1',	true ],
			['A_1',	true ],
			['A_B_C',true],
			['1A'	,false],
			];
    }	
}

?>	