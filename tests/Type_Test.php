<?php
use ABridge\ABridge\Mtype;

class Type_Test extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider Provider1
     */
 
    public function testcheckType($a, $b, $expected)
    {
        $this->assertEquals($expected, Mtype::checkType($a, $b));
    }
 
    public function Provider1()
    {
        return [
            ['x',   'not',      0],
            [null,  Mtype::M_INT,      1],
            ['1',   Mtype::M_INT,      0],
            [1,     Mtype::M_INT,      1],
            [1.5,   Mtype::M_INT,      0],
            [null,  Mtype::M_FLOAT,    1],
            ['1',   Mtype::M_FLOAT,    0],
            [1,     Mtype::M_FLOAT,    0],
            [1.5,   Mtype::M_FLOAT,    1],
            [null,  Mtype::M_STRING,   1],
            ['1',   Mtype::M_STRING,   1],
            [1,     Mtype::M_STRING,   0],
            [1.5,   Mtype::M_STRING,   0],
            [0,     Mtype::M_BOOL,         0],
            ['x',   Mtype::M_BOOL,         0],
            [false,     Mtype::M_BOOL,         1],
            [1,     Mtype::M_BOOL,     0],
            [-1,    Mtype::M_INTP,         0],
            [1,     Mtype::M_INTP,         1],
            [0  ,   Mtype::M_INTP,         0],
            ['1',   Mtype::M_INTP,     0],
            [null,  Mtype::M_ALNUM,    1],
            [1,     Mtype::M_ALNUM,    0],
            ['1',   Mtype::M_ALNUM,    1],
            ['A1',  Mtype::M_ALNUM,    1],
            [1,     Mtype::M_ALPHA,    0],
            ['1',   Mtype::M_ALPHA,    0],
            ['A1',  Mtype::M_ALPHA,    0],
            ['Abb',     Mtype::M_ALPHA,    1],
            [null,                      Mtype::M_DATE,         1],
            [-1,                        Mtype::M_DATE,         0],
            ['2016-10-25',              Mtype::M_DATE,         1],
            [' 2016-10-25 12:30:48',    Mtype::M_DATE,         0],
            ['1959-05-26'           ,   Mtype::M_DATE,         1],
            ['1959-5-59'            ,   Mtype::M_DATE,         0],
            ['1959-02-31'           ,   Mtype::M_DATE,         0],
            ['2016-02-28'           ,   Mtype::M_DATE,         1],
            ['2016-02-29'           ,   Mtype::M_DATE,         1],
            ['2016-10-25 12:30:48'  ,   Mtype::M_TMSTP,    1],
            ['25-10-2016'           ,   Mtype::M_TMSTP,    0],
            ['now'                  ,   Mtype::M_TMSTP,    0],
            ['now          vvvvvv'  ,   Mtype::M_TXT,      1],
            ['now  </br>   vvvvvv'  ,   Mtype::M_TXT,      0],
            ['now  </br>   vvvvvv'  ,   Mtype::M_RTXT,         1],
            ['["x","y",{"x":"y<>z"}]'   ,   Mtype::M_JSON,         1],
            ['["x","y",{"x":"y<>z"},]'  ,   Mtype::M_JSON,         0],
            ];
    }

    /**
     * @dataProvider Provider_isStruct
     */
 
    public function testisStruc($a, $expected, $e2)
    {
        $this->assertEquals($expected, Mtype::isStruct($a));
        $this->assertEquals($e2, Mtype::isRaw($a));
    }
    
    public function Provider_isStruct()
    {
        return [
        [Mtype::M_INT,        true,   false],
        [Mtype::M_JSON,   false,  true]
        ];
    }

    /**
     * @dataProvider Provider2
     */
 
    public function testisMtype($a, $expected)
    {
        $this->assertEquals($expected, Mtype::isMtype($a));
    }
    
    public function Provider2()
    {
        return [
        [Mtype::M_INT,        1],
        ['nint',   0],
        ];
    }
 
    /**
     * @dataProvider Provider3
     */
 
    public function testconvertString($X, $Type, $expected)
    {
        $this->assertEquals($expected, Mtype::convertString($X, $Type));
    }
    
    public function Provider3()
    {
        return [
            ['',        Mtype::M_INT,      null],
            ['1',       Mtype::M_INT,      1],
            ['1',       Mtype::M_CODE,         1],
            ['',        Mtype::M_CODE,         null],
            [ "true",   Mtype::M_BOOL,         true],
            [ "false",  Mtype::M_BOOL,         false],
            ['1.5',     Mtype::M_FLOAT,    1.5],
            ['x',       Mtype::M_FLOAT,    'x'],
            ['x',       Mtype::M_BOOL,     'x'],
            ["pp",      Mtype::M_INT,      "pp"],
            ["pp",      Mtype::M_TXT,      "pp"],
            ["pp",      Mtype::M_RTXT,         "pp"],
            ];
    }
    
    
    /**
     * @dataProvider Provider4
     */
    function testconvertSqlType($x, $expected)
    {
        $this->assertEquals($expected, Mtype::convertSqlType($x));
    }
    
    public function Provider4()
    {
        return [
            [Mtype::M_INT,         'INT(11)'],
            [Mtype::M_TMSTP,   'TIMESTAMP'],
            [Mtype::M_INTP,    'INT(11) UNSIGNED'],
            [Mtype::M_STRING,  'VARCHAR(255)'],
            [Mtype::M_FLOAT,   'FLOAT'],
            [Mtype::M_CREF,    'INT(11) UNSIGNED'],
            [Mtype::M_BOOL,    'BOOLEAN'],
            [Mtype::M_ALNUM,   'VARCHAR(255)'],
            [Mtype::M_ALPHA,   'VARCHAR(255)'],
            [Mtype::M_DATE,    'DATE'],
            [Mtype::M_TXT,     'TEXT'],
            [Mtype::M_RTXT,    'TEXT'],
            ['notexists',   false],
            ];
    }
    
    /**
     * @dataProvider Provider5
     */
    function testidentif($x, $expected)
    {
        $this->assertEquals($expected, Mtype::checkIdentifier($x));
    }

    public function Provider5()
    {
        return [
            ['1',   false],
            ['_',   true ],
            ['A',   true ],
            ['A1',  true ],
            ['A_1',     true ],
            ['A_B_C',true],
            ['1A'   ,false],
            ];
    }
}
