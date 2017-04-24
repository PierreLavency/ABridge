<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 


class Model_Slc_Test extends PHPUnit_Framework_TestCase  
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
		$Student=get_called_class().'_1';
		
		self::$db1=getBaseHandler ($typ, $name);

		initStateHandler ($Student	,$typ, $name);
		$Student=get_called_class().'_f_1';	
		$typ='fileBase';
		
		self::$db2=getBaseHandler ($typ, $name);

		initStateHandler ($Student	,$typ, $name);
		
	}
	
	public function setTyp ($typ) 
	{
		if ($typ== 'SQL') {
			$this->db=self::$db1;
			$this->Student=get_called_class().'_1';

			} 
		else {
			$this->db=self::$db2;
			$this->Student=get_called_class().'_f_1';
			}

	}
	
	public function Provider1() 
	{
		return [['SQL'],['FLE']];
	}	
	/**
     * @dataProvider Provider1
     */

	public function testSelect($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$this->assertNotNull($x = new Model($this->Student));
		$res=$x->deleteMod();
			$x->getErrLog()->show();		
		$this->assertTrue($res);

		$x->addAttr('a',M_STRING);
		$x->addAttr('b',M_INT);		
		$x->addAttr('c',M_DATE);
		
		$x->getErrLog()->show();
		$this->assertFalse($x->isErr());

		$res = $x->saveMod();	
		$this->assertTrue($res);
		
		$n = 20;
        for ($i=0;$i<$n;$i++) {
			$x = new Model($this->Student);
			$a = 'Name_'. $i;
			$x->setVal('a',$a);
			$x->setVal('b',$i);

			$x->setVal('c','1959-05-26');
			$x->save();
			$x->getErrLog()->show();
			$this->assertFalse($x->isErr());
		}

		$this->assertTrue($x->setCriteria(['a','b','c'],[],['Name_1',1,'1959-05-26']));
		$this->assertEquals(1, count($x->select()));
		$this->assertTrue($x->setCriteria(['a'],['a'=>'>'],['Name_1']));
		$this->assertEquals(18, count($x->select()));	
		$this->assertTrue($x->setCriteria(['b'],['b'=>'>'],[1]));
		$this->assertEquals(18, count($x->select()));
		$this->assertTrue($x->setCriteria(['c'],['b'=>'>'],['1959-05-26']));
		$this->assertEquals(20, count($x->select()));	
		$x->protect('a');
		$this->assertEquals(1, count($x->select()));
		
		$db->commit();

	}
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends  testSelect
    */

	public function testerr($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$x = new Model($this->Student,1);
		$this->assertFalse($x->setCriteria(['a','b','xx'],[],['Name_1',1,'1959-05-26']));
		$this->assertEquals($x->getErrLine(),E_ERC002.':'.'xx');
		
		$db->commit();
	}
	

}
