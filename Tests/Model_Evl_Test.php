<?php
	

require_once("Model.php"); 

class testeval {
	private $_mod;
	function __construct($mod) {
		$this->_mod=$mod;
	}
	public function getVal($attr) {
		if ($attr == 'aplusb') {
			$a = $this->_mod->getVal('a');
			$b = $this->_mod->getVal('b');
			return $a+$b;
		}
	}
	
}

class Model_Evl_Test extends PHPUnit_Framework_TestCase {

 	public function testNew()
    {
		$CName ='testeval';

		$this->assertNotNull(($x = new Model($CName)));

		$this->assertTrue($x->addAttr('a',M_INT));
		$this->assertTrue($x->addAttr('b',M_INT));
		$this->assertTrue($x->addAttr('aplusb',M_INT,M_P_EVAL));
		$this->assertFalse($x->isErr());
		
		return $x;
	}		
	
	/**
    * @depends testNew
    */

	public function testval($x)
    {

		$this->assertTrue($x->setVal('a',1));
		$this->assertTrue($x->setVal('b',1));
		$this->assertEquals(2,$x->getVal('aplusb'));

		$this->assertTrue($x->setVal('a',2));
		$this->assertEquals(3,$x->getVal('aplusb'));
		
		return $x;
	}

	/**
    * @depends testval
    */

	public function testErrdel($x)
    {	
	
		$this->assertFalse($x->setVal('aplusb',1));
		$this->assertEquals($x->getErrLine(),E_ERC039.':aplusb');
		
		$this->assertTrue($x->delAttr('aplusb'));
		
		$this->assertNotNull(($y = new Model('toto')));
		$this->assertFalse($y->addAttr('aplusb',M_INT,M_P_EVAL));
		$this->assertEquals($y->getErrLine(),E_ERC040.':aplusb:'.M_INT);
		
		$this->assertFalse($y->addAttr('aplusb',M_INT,'/xx'));
		$this->assertEquals($y->getErrLine(),E_ERC041.':aplusb:'.M_INT.':/xx');
		
	}

	
}
