<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

class testevalP 
{
	private $_mod;
	function __construct($mod) 
	{
		$this->_mod=$mod;
	}
	
	
	public function save()
	{
		$a = $this->_mod->getVal('a');
		$b = $this->_mod->getVal('b');
		$this->_mod->setVal('aplusb',$a+$b);
		if (!$a and $this->_mod->getId()) {
			$this->_mod->getErrLog()->logLine('wrong');
			return false;
		}
		return true;
	}
	
	public function afterDelet() 
	{
		return true;
	}	

	public function delet()
	{
		$a = $this->_mod->getVal('a');
		if (!$a and $this->_mod->getId()) {
			$this->_mod->getErrLog()->logLine('wrong');
			return false;
		}
		return true;
	}
	public function afterSave()
	{
		return true;
	}
}
class testevalPF 
{
	private $_mod;
	private $_x;
	function __construct($mod) 
	{
		$this->_mod=$mod;
	}
	
	
	public function save()
	{
		$a = $this->_mod->getVal('a');
		$b = $this->_mod->getVal('b');
		$this->_mod->setVal('aplusb',$a+$b);
		if (!$a and $this->_mod->getId()) {
			$this->_mod->getErrLog()->logLine('wrong');
			return false;
		}
		return true;
	}
	
	public function delet()
	{
		$a = $this->_mod->getVal('a');
		if (!$a and $this->_mod->getId()) {
			$this->_mod->getErrLog()->logLine('wrong');
			return false;
		}
		return true;
	}
	public function afterSave()
	{
		return true;
	}
	public function afterDelet() 
	{
		return true;
	}
}
class Model_Evp_Test extends PHPUnit_Framework_TestCase  
{
	protected static $db1;
	protected static $db2;

	protected $Student='testevalP';
	protected $db;

	
	public static function setUpBeforeClass()
	{	
	
		resetHandlers();
		$typ='dataBase';
		$name='test';	
		$Student='testevalP';
		
		self::$db1=getBaseHandler ($typ, $name);

		initStateHandler ($Student	,$typ, $name);
		$Student='testevalPF';	
		$typ='fileBase';
		
		self::$db2=getBaseHandler ($typ, $name);

		initStateHandler ($Student	,$typ, $name);
		
	}
	
	public function setTyp ($typ) 
	{
		if ($typ== 'SQL') {
			$this->db=self::$db1;
			$this->Student='testevalP';

			} 
		else {
			$this->db=self::$db2;
			$this->Student='testevalPF';

			}

	}
	
	public function Provider1() 
	{
		return [['SQL'],['FLE']];
	}	
	/**
     * @dataProvider Provider1
     */

	public function testNew($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$this->assertNotNull($x = new Model($this->Student));
		$res=$x->deleteMod();
		$this->assertTrue($res);
		
		$this->assertTrue($x->addAttr('a',M_INT));
		$this->assertTrue($x->addAttr('b',M_INT));
		$this->assertTrue($x->addAttr('aplusb',M_INT,M_P_EVALP));
		$this->assertFalse($x->isErr());

		$res = $x->saveMod();	
		$this->assertTrue($res);			
		$db->commit();

	}
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testNew
    */
	public function testsave($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$this->assertNotNull($x = new Model($this->Student));
		$res= $x->setVal('a',1);
		$this->assertTrue($res);
		$this->assertTrue($x->setVal('b',1));
		
		$res=$x->save();
		$this->assertEquals(1,$res);
		$db->commit();
	}
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends  testsave
    */
	
	public function testget($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$this->assertNotNull($x = new Model($this->Student,1));
		$res= $x->getVal('aplusb');
		$this->assertEquals(2,$res);
		
		$res=$x->isOptl('aplusb');
		$this->assertFalse($res);
		
		$db->commit();
	}
	
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends  testget
    */
	public function testerr($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$this->assertNotNull($x = new Model($this->Student,1));
		$res= $x->setVal('aplusb',1);
		$res= $x->setVal('a',1);
		$this->assertTrue($res);
		
		$this->assertEquals($x->getErrLine(),E_ERC042.':'.'aplusb');

		$res= $x->setVal('a',0);
		$this->assertTrue($res);
		
		$res = $x->save();
		$this->assertFalse($res);
		$this->assertEquals($x->getErrLine(),'wrong');

		$res = $x->delet();
		$this->assertFalse($res);
		$this->assertEquals($x->getErrLine(),'wrong');
		
		$db->commit();
	}
	
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends  testerr
    */
	public function testdel($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		$this->assertNotNull($x = new Model($this->Student,1));
		
		$res= $x->delAttr('aplusb');
		$this->assertTrue($res);
		
		$res= $x->delet();

		$this->assertTrue($res);	
		$db->commit();
	}	
}
