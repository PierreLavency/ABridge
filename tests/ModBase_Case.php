<?php
	
/* */
require_once("Model.php"); 
require_once("ModBase.php"); 

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
		$this->assertTrue($mod->addAttr('Name'));
		$this->assertTrue($mod->addAttr('Surname'));
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

		$db->commit();
	}

	/**
    * @depends testrestoreMod
    */

 	public function testSaveObj()
    {		
		$db=self::$db; 	
		$db->beginTrans();		
		$this->assertNotNull($sh=new ModBase($db));		
		$this->assertNotNull($mod=new Model(self::$CName));
		$this->assertTrue($sh->restoreMod($mod));
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

?>	