<?php
	
/* */
require_once("Src\Model.php"); 
require_once("Src\ModBase.php"); 

class ModBase_Case extends PHPUnit_Framework_TestCase {

	protected static $CName;
	protected static $DBName;
	protected static $db;
	
	protected $hdlr;

	public function testSaveMod() 
	{
		$db=self::$db; 
		$db->beginTrans();

		$this->assertNotNull($sh=new ModBase($db));		
		$this->assertNotNull($mod=new Model(self::$CName));
		$this->assertTrue($sh->eraseMod($mod));
		$this->assertTrue($mod->addAttr('Name',M_STRING));
		$this->assertTrue($mod->addAttr('Surname',M_STRING));
		$this->assertTrue($sh->saveMod($mod));

		$this->hdlr = $sh;

		$db->commit();

	}

	/**
    * @depends testSaveMod
    */
	public function testrestoreMod() 
	{
		$db=self::$db; 
		$db->beginTrans();
		
	
		$this->assertNotNull($sh=new ModBase($db));		
		$this->assertNotNull($mod=new Model(self::$CName));
		$this->assertTrue($sh->restoreMod($mod));
		$this->assertTrue($mod->existsAttr('Name'));
		$this->assertTrue($mod->existsAttr('Surname'));


		$this->assertTrue($mod->delAttr('Surname'));
		$this->assertTrue($mod->addAttr('Age',M_STRING));
		$this->assertTrue($sh->saveMod($mod));


		$db->commit();
	}

	/**
    * @depends testrestoreMod
    */
		public function testrestoreMod1() 
	{
		$db=self::$db; 
		$db->beginTrans();

		$this->assertNotNull($sh=new ModBase($db));		
		$this->assertNotNull($mod=new Model(self::$CName));
		$this->assertTrue($sh->restoreMod($mod));
		$this->assertTrue($mod->existsAttr('Name'));
		$this->assertFalse($mod->existsAttr('Surname'));
		$this->assertTrue($mod->existsAttr('Age'));
		
		$this->assertTrue($mod->addAttr('Surname',M_STRING));
		$this->assertTrue($mod->delAttr('Age'));
		$this->assertTrue($sh->saveMod($mod));


		$db->commit();
	}
	
	/**
    * @depends testrestoreMod1
    */

 	public function testSaveObj()
    {		
		$db=self::$db; 	
		$db->beginTrans();

		$this->assertNotNull($sh=new ModBase($db));		
		$this->assertNotNull($mod=new Model(self::$CName));
		$this->assertTrue($sh->restoreMod($mod));
		$this->assertFalse($mod->existsAttr('Age'));
		$this->assertTrue($mod->setVal('Name','Lavency'));
		$this->assertTrue($mod->setVal('Surname','Pierre'));
		$this->assertEquals(1,$sh->saveObj($mod));
	
		$db->commit();
	}		
	
	/**
    * @depends testSaveObj
    */

	public function testRestoreObj()
    {
		$x = self::$db;	
		$x->beginTrans();
		$this->assertNotNull($sh=new ModBase($x));
		$this->assertNotNull($mod=new Model(self::$CName));
		$this->assertTrue($sh->restoreMod($mod));
		$this->assertFalse($sh->restoreObj($mod));		
		$this->assertNotNull($mod=new Model(self::$CName,1));
		$this->assertTrue($sh->restoreMod($mod));
		$this->assertEquals(1,$sh->restoreObj($mod));
		$this->assertEquals('Lavency',$mod->getVal('Name'));
		$this->assertEquals('Pierre',$mod->getVal('Surname'));
		$this->assertTrue($sh->eraseObj($mod));
	
		$x->commit();
	}
	
	/**
    * @depends testRestoreObj
    */

	public function testEraseObj()
    {
		$x = self::$db;	
		$x->beginTrans();

		$this->assertNotNull($sh=new ModBase($x));	
		$this->assertNotNull($mod=new Model(self::$CName));
		$this->assertTrue($sh->restoreMod($mod));
		$this->assertTrue($sh->eraseObj($mod));
		
		$this->assertNotNull($mod=new Model(self::$CName,1));
		$this->assertTrue($sh->restoreMod($mod));
		$this->assertEquals($sh->restoreObj($mod),0);
		$this->assertTrue($sh->eraseMod($mod));
		
		$x->commit();
	}
	
	/**
    * @depends testEraseObj
    */

    public function testEraseMod() 
	{
		$x = self::$db;	
		$x->beginTrans();

		$this->assertNotNull($sh=new ModBase($x));
		$this->assertNotNull($mod=new Model(self::$CName,1));
		$this->assertFalse($sh->restoreMod($mod));
		
		$x->commit();
	}

	
	
}
