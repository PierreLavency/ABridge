<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

class Model_Abst_2_Test extends PHPUnit_Framework_TestCase  
{
	protected static $db1;
	protected static $db2;


	protected $Application='Application';		
	protected $AppType='AppType';		
	protected $Code='Code';
	protected $Exchange='Exchange';
	protected $db;
	protected $napp=2;
	protected $ncomp=5;
	
	
	public static function setUpBeforeClass()
	{	
	
		resetHandlers();
		$typ='dataBase';
		$name='atest';	
		$Application='Application';		
		$AppType='AppType';		
		$Code='Code';
		$Exchange='Exchange';
		
		self::$db1=getBaseHandler ($typ, $name);
		initStateHandler ($Application		,$typ, $name);
		initStateHandler ($AppType	,$typ, $name);
		initStateHandler ($Code	,$typ, $name);
		initStateHandler ($Exchange	,$typ, $name);
		
		$typ='fileBase';
		$name=$name.'_f';
		$Application='Applicationf';		
		$AppType='AppTypef';		
		$Code='Codef';
		$Exchange='Exchangef';		
		self::$db2=getBaseHandler ($typ, $name);
		initStateHandler ($Application		,$typ, $name);
		initStateHandler ($AppType	,$typ, $name);
		initStateHandler ($Code	,$typ, $name);
		initStateHandler ($Exchange	,$typ, $name);
		
	}
	
	public function setTyp ($typ) 
	{
		if ($typ== 'SQL') {
			$this->db=self::$db1;
			$this->Code='Code';
			$this->Application='Application';
			$this->AppType='AppType';
			$this->Exchange='Exchange';			
			} 
		else {
			$this->db=self::$db2;
			$this->Code='Codef';
			$this->Application='Applicationf';
			$this->AppType='AppTypef';
			$this->Exchange='Exchangef';					
			}

	}
	
	public function Provider1() 
	{
		return [['SQL'],['FLE']];
	}	
	/**
     * @dataProvider Provider1
     */

	public function testSaveMod($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$Code = new Model($this->Code);
		$res= $Code->deleteMod();

		$res = $Code->addAttr('Name',M_STRING); 
		$res = $Code->setAbstr(); 	
		$res = $Code->saveMod();	
		$this->assertFalse($Code->isErr());	
				
		$AppType = new Model($this->AppType);
		$res= $AppType->deleteMod();
		$res = $AppType->setInhNme($this->Code);
		$res = $AppType->saveMod();	
		$this->assertFalse($AppType->isErr());			
		
		$Application = new Model($this->Application);
		$res= $Application->deleteMod();
		$res = $Application->addAttr('Name',M_STRING);		
		$res = $Application->addAttr('Type',M_CODE,'/'.$this->AppType); 
		$res = $Application->saveMod();	
		$this->assertFalse($Application->isErr());		

		$db->commit();
	}

	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testSaveMod
    */
	public function testNewType($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$obj=new Model($this->AppType);
		$this->assertNotNull($obj);	
		
		$this->assertTrue($obj->existsAttr('Name'));
		$this->assertTrue($obj->setVal('Name','type'));
		
		$res= $obj->save();
		$this->assertFalse($obj->isErr());	
			
		$db->commit();
		
	}
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testNewType
    */
	public function testSetType($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$obj=new Model($this->Application);
		$this->assertNotNull($obj);
		$obj->setVal('Name','Test');
		$res = $obj->getValues('Type');
		
		$this->assertEquals(1,count($res));
		$this->assertTrue($obj->setVal('Type',1));
		
		$res= $obj->save();
		$this->assertFalse($obj->isErr());	
		
		$db->commit();
		
	}
	
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testNewType
    */
	public function testModCode($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$obj = new Model($this->Code);
		$res = $obj->addAttr('SurName',M_STRING); 
		
		$res= $obj->saveMod();
		echo $obj->getErrLine();
		$this->assertFalse($obj->isErr());	
		
		$obj = new Model($this->AppType);
		$this->assertTrue($obj->existsAttr('SurName'));	
			
		$this->assertTrue($obj->setVal('SurName','toto'));

		$res= $obj->save();
		$this->assertFalse($obj->isErr());	
			
		$db->commit();
	
	}
	
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testModCode
    */
	public function testDelCode($typ) 	
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$obj = new Model($this->Code);
		$res = $obj->delAttr('SurName'); 
		
		$obj->saveMod();
		$this->assertFalse($obj->isErr());	
		
		$obj = new Model($this->AppType);
		$this->assertFalse($obj->existsAttr('SurName'));	
		
	}
}

?>	