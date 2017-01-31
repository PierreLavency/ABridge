<?php
	
require_once("Home.php"); 

class Home_Test extends PHPUnit_Framework_TestCase  
{
	protected static $db1;
	protected static $db2;

	protected $CName='Example';
	protected $CUser='User';

	protected $db;
	
	
	public static function setUpBeforeClass()
	{	
	
		resetHandlers();
	
		$typ='dataBase';
		$CName='Example';
		$CUser='User';
		$name = 'test';
		self::$db1=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		initStateHandler ($CUser	,$typ, $name);
		
		$typ='fileBase';
		$name=$name.'_f';
		$CName='Examplef';
		$CUser='Userf';
		self::$db2=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		initStateHandler ($CUser	,$typ, $name);
		
	}
	
	public function setTyp($typ) 
	{
		if ($typ== 'SQL') {
			$this->db=self::$db1;
			$this->CName='Example';
			$this->CUser='User';
			} 
		else {
			$this->db=self::$db2;
			$this->CName='Examplef';
			$this->CUser='Userf';
			}

	}
	
	public function Provider1() 
	{
		return [['SQL'],['FLE']];
	}	
	/**
     * @dataProvider Provider1
     */

	public function testRoot($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$mod = new Model($this->CUser);	
		$res = $mod->deleteMod();
		$res = $mod->saveMod();

		$u1  = $mod->save();			
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);
		
		$mod = new Model($this->CUser);	
		$u2  = $mod->save();		
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);
		
		$mod = new Model($this->CName);	
		$res= $mod->deleteMod();
		$res = $mod->addAttr('Ref',M_REF,'/'.$this->CName);
		$res = $mod->addAttr('CRef',M_CREF,'/'.$this->CName.'/Ref');
		$res = $mod->saveMod();

		$id1 = $mod->save();
		$obj1 = $mod;		
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);
		
		$mod = new Model($this->CName);	
		$res=$mod->setVal('Ref',$id1);	
		$id2 = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);

		$h = new Home();
		$this->assertNotNull($h);
		$this->assertTrue($h->isRoot());
		$this->assertNull($h->getObj());
		$this->assertTrue($h->canLink($obj1));
		$this->assertTrue($h->isLinked($obj1));
		$this->assertTrue($h->hlink($obj1));

		$h= new Home('/'.$this->CUser.'/1');
		$this->assertNotNull($h);
		$this->assertFalse($h->isRoot());
		$this->assertFalse($h->canLink($obj1));
		$this->assertFalse($h->isLinked($obj1));
		$this->assertFalse($h->hlink($obj1));

		$mod = new Model($this->CName);	
		$res = $mod->addAttr($this->CUser,M_REF,'/'.$this->CUser);
		$res = $mod->saveMod();
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);

		
		$obj1 = new Model($this->CName,$id1);	
		$this->assertTrue($h->canLink($obj1));
		$this->assertFalse($h->isLinked($obj1));
		$this->assertTrue($h->hlink($obj1));
		$this->assertTrue($h->isLinked($obj1));
		$this->assertTrue($h->hlink($obj1));
		
		$obj0 = new Model($this->CName);	
		$this->assertTrue($h->canLink($obj0));
		$this->assertFalse($h->isLinked($obj0));
		$this->assertTrue($h->hlink($obj0));

		$this->assertFalse($h->canLink(null));
		$this->assertFalse($h->isLinked(null));		
		$this->assertFalse($h->hlink(null));	


		$h= new Home('/'.$this->CUser.'/2');
		$this->assertTrue($h->canLink($obj1));
		$this->assertFalse($h->isLinked($obj1));
		$this->assertFalse($h->hlink($obj1));
		
		$db->commit();
	}
	

		
}
