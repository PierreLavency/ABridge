<?php
	
/* */

require_once("SQLBase.php"); 

class Base_Case extends PHPUnit_Framework_TestCase {

	protected $test1 =['CODE'=> '001', 'SEVERITY'=> 1];
	protected $test2 =['CODE'=> '002', 'SEVERITY'=> 2];
	protected $test3 =['CODE'=> '001', 'CODE'=> 0];
	protected $meta=[
						'attr_lst'=>['vnum','ctstp','utstp','CODE','SEVERITY',],
						'attr_typ'=> ["vnum"=>M_INT,"ctstp"=>M_TMSTP,"utstp"=>M_TMSTP,'CODE'=>M_STRING,'SEVERITY'=>M_INT,],
					];
	
	protected $id1=1;
	protected $id2=2;
	
	protected static $CName;
	protected static $CName2;
	protected static $DBName;
	protected static $db;

	public function testNewMod() 
	{
		$db=self::$db; 
		$db->beginTrans();
		$this->assertTrue($db->newMod(self::$CName,$this->meta));
		$this->assertTrue($db->newMod(self::$CName2,[]));
		$db->commit();
	}

	/**
    * @depends testNewMod
    */
	public function testPutMod() 
	{
		$db=self::$db; 
		$db->beginTrans();
		$this->assertEquals($this->meta,$db->getMod(self::$CName));
		$this->assertEquals([],$db->getMod(self::$CName2));
		$this->assertTrue($db->putMod(self::$CName,[],[],$this->meta));
		$this->assertTrue($db->putMod(self::$CName2,$this->meta,$this->meta,[]));
		$db->commit();
	}

	/**
    * @depends testPutMod
    */
	public function testDelMod() 
	{
		$db=self::$db; 
		$db->beginTrans();
		$this->assertEquals([],$db->getMod(self::$CName));
		$this->assertEquals($this->meta,$db->getMod(self::$CName2));		
		$this->assertTrue($db->delMod(self::$CName2,$this->meta));
		$db->commit();
	}
	/**
    * @depends testDelMod
    */	
	
 	public function testNewObj()
    {		
		$db=self::$db; 	
		$db->beginTrans();		
		$this->assertTrue($db->putMod(self::$CName,$this->meta,$this->meta,[]));	
		$this->assertFalse($db->existsMod(self::$CName2));
		$this->assertEquals($this->id1,self::$db->newObj(self::$CName,$this->test1));		
		$this->assertEquals($this->id2,self::$db->newObj(self::$CName,$this->test2));
		self::$db->commit();
	}		
	
	/**
    * @depends testNewObj
    */

	public function testPutObj()
    {
		$x = self::$db;	
		$x->beginTrans();
		$this->assertEquals($this->test1,$x->getObj(self::$CName,$this->id1));	
		$this->assertEquals($this->test2,$x->getObj(self::$CName,$this->id2));	
		$this->assertEquals($this->id1,$x->putObj(self::$CName,$this->id1,$this->test2));
		$this->assertEquals($this->id2,$x->putObj(self::$CName,$this->id2,$this->test1));		
		$x->commit();
	}
	/**
    * @depends testPutObj
    */

	public function testDelObj()
    {
		$x = self::$db;	
		$x->beginTrans();
		$this->assertEquals($this->test2,$x->getObj(self::$CName,$this->id1));	
		$this->assertEquals($this->test1,$x->getObj(self::$CName,$this->id2));	
		$this->assertTrue($x->delObj(self::$CName,$this->id2));		
		$x->commit();
		
	}
	/**
    * @depends testDelObj
    */

    public function testRollBack() 
	{
		$x = self::$db;	
		$x->beginTrans();
		$this->assertEquals(0,$x->getObj(self::$CName,$this->id2));	
		$this->assertTrue($x->delObj(self::$CName,$this->id1));		
		$r = $x->rollback();
		$x->beginTrans();
		$this->assertEquals($this->test2,$x->getObj(self::$CName,$this->id1));			
	}

	/**
    * @depends  testRollBack
    */
	
	public function testFindObj() 
	{
		$x = self::$db;	
		$x->beginTrans();
		$n=10;
		for ($i=0;$i<$n;$i++) {
			$code = '0'.$i;
			$j=$i;
			if ($i < ($n/2)) {$j=1;}
			$test= ['CODE'=>$code,'SEVERITY'=>$j];
			$id = $x->newObj(self::$CName,$test);
		}
		$this->assertEquals($id,($n+2));
		$this->assertEquals(($n/2),count($x->findObj(self::$CName,'SEVERITY',1)));
		$this->assertEquals(1,count($x->findObj(self::$CName,'CODE','01')));
		$x->commit();
		
	}
	
	
}

?>	