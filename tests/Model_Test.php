<?php
	

require_once("Model.php"); 


class Model_Test extends PHPUnit_Framework_TestCase {

 	public function testNew()
    {
		$CName ='test';

		try {$x=new Model(1);} catch (Exception $e) {$r= 'Exception reçue : '. $e->getMessage();}
		$this->assertEquals($r, 'Exception reçue : ERC010:1:m_alpha');
		
		try {$x=new Model('$',1);} catch (Exception $e) {$r= 'Exception reçue : '. $e->getMessage();}
		$this->assertEquals($r, 'Exception reçue : ERC010:$:m_alpha');
		
		try {$x=new Model($CName,-1);} catch (Exception $e) {$r= 'Exception reçue : '. $e->getMessage();}
		$this->assertEquals($r, 'Exception reçue : ERC011:-1:m_intp');

		try {$x=new Model($CName,0);} catch (Exception $e) {$r= 'Exception reçue : '. $e->getMessage();}
		$this->assertEquals($r, 'Exception reçue : ERC011:0:m_intp');		

		$this->assertNotNull(($x = new Model($CName)));
		
		$this->assertEquals($CName, $x->getModName());
					
		$this->assertEquals(0, $x->getVal('id'));
		
		return $x;
	}		
	
	/**
    * @depends testNew
    */

	public function testAddDel($x)
    {
		$this->assertTrue($x->addAttr('y'));
		
		$this->assertTrue($x->delAttr('y'));

		$this->assertTrue($x->addAttr('a1'));

		$this->assertTrue($x->existsAttr("id"));
		
		$this->assertTrue($x->isPredef("id"));
		
		$this->assertTrue($x->existsAttr("a1"));

		$this->assertFalse($x->existsAttr("a2"));
		
		$this->assertFalse($x->existsAttr("y"));
		
		return $x;
	}

	/**
    * @depends testAddDel
    */

	public function testGetSet($x)
    {	
		$this->assertNull($x->getVal("a1"));

		$this->assertTrue($x->setVal('a1','A'));
	
		$this->assertTrue($x->setVal("a1",'A1'));

		$this->assertFalse($x->setVal("id",2));
		
		$this->assertEquals('A1', $x->getVal("a1"));

		$this->assertFalse($x->setVal("a2",'A'));

		$this->assertTrue($x->addAttr('a2'));
		
		$this->assertTrue($x->setVal("a2",'A2'));
		
		$this->assertEquals('id , vnum , ctstp , utstp , a1 , a2', implode (' , ',$x->getAllAttr())  );

    }
    
}

?>	