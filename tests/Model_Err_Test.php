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
		
		$this->assertFalse($x->addAttr('id',M_STRING));
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
		$n =6;
		
		$n++;
		$this->assertFalse($x->getVal('y'));
		$this->assertEquals($log->getLine($n),'ERC002:y');
		
		$n++;
		$this->assertFalse($x->setVal('y',3));
		$this->assertEquals($log->getLine($n),'ERC002:y');
		
		$n++;
		$this->assertFalse($x->setVal('id',3));
		$this->assertEquals($log->getLine($n),'ERC001:id');
		
		$n++;
		$this->assertTrue($x->addAttr('y',M_INT));
		$this->assertFalse($x->setVal('y','A'));
		$this->assertEquals($log->getLine($n),'ERC005:y:A:m_int');
		
		$n++;
		$this->assertFalse($x->getTyp('notexists'));
		$this->assertEquals($log->getLine($n),'ERC002:notexists');
		return $x;
		
	}/**
    * @depends testGetSet
    */
	
    public function testSave($x) 
	{
		$log = $x->getErrLog ();
		$n = 11;

		$n++;
		$this->assertFalse($x->save());
		$this->assertEquals($log->getLine($n),'ERC006');
		
		$n++;
		$this->assertFalse($x->delet());
		$this->assertEquals($log->getLine($n),'ERC006');
		
		$n++;
		$this->assertFalse($x->saveMod());
		$this->assertEquals($log->getLine($n),'ERC006');

		$n++;		
		$this->assertFalse($x->deleteMod());
		$this->assertEquals($log->getLine($n),'ERC006');
		
	}
}

?>	