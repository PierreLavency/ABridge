<?php
	
/* all errors that does not need persistency 001 -> 006*/

require_once("Model.php"); 


class Model_Err_Test extends PHPUnit_Framework_TestCase {

 	public function testNew()
    {
		$CName ='test';

		$this->assertNotNull(($x = new Model($CName)));
		
		$this->assertEquals($CName, $x->getModName());
					
		return $x;
	}		
	
	/**
    * @depends testNew
    */

	public function testAddDel($x)
    {
		$log = $x->getErrLog ();

		$this->assertFalse($x->isErr());
		
		$this->assertTrue($x->isPredef('id'));
		
		$this->assertFalse($x->delAttr('id'));
		$this->assertEquals($log->getLine(0),'ERC001:id');
		
		$this->assertTrue($x->isErr());
		
		$this->assertFalse($x->delAttr('x'));
		$this->assertEquals($log->getLine(1),'ERC002:x');
		
		$this->assertFalse($x->addAttr('id'));
		$this->assertEquals($log->getLine(2),'ERC003:id');
		
		$this->assertFalse($x->addAttr('x','notexists'));
		$this->assertEquals($log->getLine(3),'ERC004:notexists');
		
		$this->assertFalse($x->addAttr('x',M_REF));
		$this->assertEquals($log->getLine(4),"ERC008:x:m_ref");
		
		$this->assertFalse($x->addAttr('x',M_REF,'notexists'));
		$this->assertEquals($log->getLine(5),'ERC014:x:m_ref');

		$this->assertFalse($x->isPredef('x'));
		$this->assertEquals($log->getLine(6),'ERC002:x');
		return $x;
	}
	/**
    * @depends testAddDel
    */

	public function testGetSet($x)
    {
		$log = $x->getErrLog ();
		
		$this->assertFalse($x->getVal('y'));
		$this->assertEquals($log->getLine(7),'ERC002:y');
		
		$this->assertFalse($x->setVal('y',3));
		$this->assertEquals($log->getLine(8),'ERC002:y');
		
		$this->assertFalse($x->setVal('id',3));
		$this->assertEquals($log->getLine(9),'ERC001:id');
		
		$this->assertTrue($x->addAttr('y',M_INT));
		$this->assertFalse($x->setVal('y','A'));
		$this->assertEquals($log->getLine(10),'ERC005:y:A:m_int');
		
		return $x;
		
	}/**
    * @depends testGetSet
    */
	
    public function testSave($x) 
	{
		$log = $x->getErrLog ();
		
		$this->assertFalse($x->save());
		$this->assertEquals($log->getLine(11),'ERC006');
	}
}

?>	