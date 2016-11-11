<?php
	
require_once("Model.php"); 
require_once("Path.php"); 

class Path_Test extends PHPUnit_Framework_TestCase  
{
	protected static $db1;
	protected static $db2;

	protected $CName='Example';
	protected $db;
	
	
	public static function setUpBeforeClass()
	{	
	
		resetHandlers();
	
		$typ='dataBase';
		$CName='Example';
		$name = 'test';
		self::$db1=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		
		$typ='fileBase';
		$name=$name.'_f';
		$CName='Examplef';
		self::$db2=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		
	}
	
	public function setTyp($typ) 
	{
		if ($typ== 'SQL') {
			$this->db=self::$db1;
			$this->CName='Example';
			} 
		else {
			$this->db=self::$db2;
			$this->CName='Examplef';
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
	

		// a 
		$mod = new Model($this->CName);
		$this->assertNotNull($mod);	
	
		$res= $mod->deleteMod();
		$this->assertTrue($res);	
		
		$res = $mod->addAttr('Name');
		$this->assertTrue($res);	
		
		$res = $mod->saveMod();	
		$this->assertTrue($res);	

		$res=$mod->setVal('Name','Lavency');
		$this->assertTrue($res);	
		
		$res = $mod->save();	
		$this->assertEquals($res,1);	
		
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);			
		
		$db->commit();

	}
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testSaveMod
    */
	public function testPath($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$path='/'.$this->CName;
		$mod = pathObj($path);
		$this->assertNotNull($mod);
		$this->assertEquals($mod->getModName(),$this->CName);
		
		$pathr=objPath($mod);
		$this->assertEquals($pathr,$path);
		
		$path=$path.'/1';
		$mod = pathObj($path);
		$this->assertNotNull($mod);
		$this->assertEquals($mod->getModName(),$this->CName);
		$this->assertEquals($mod->getId(),1);
		
		$pathr=objPath($mod);
		$this->assertEquals($pathr,$path);

		$pathr=objAbsPath($mod);
		$this->assertEquals($pathr,rootPath().$path);
		
		$path=$path.'/Name';
		$val = pathVal($path);
		$this->assertNotNull($val);
		$this->assertEquals($val,'Lavency');
		
		$path='/';
		$mod = pathObj($path);
		$this->assertFalse($mod);
		
		$path='/'.$this->CName.'/1/Name';
		$mod = pathObj($path);
		$this->assertFalse($mod);
		
	}
}

?>	