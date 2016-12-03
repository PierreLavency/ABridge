<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

class Model_Key_Test extends PHPUnit_Framework_TestCase  
{
	protected static $db1;
	protected static $db2;


	protected $Code='Code';		
	protected $CodeVal='CodeVal';		
	protected $Student='Student';
	protected $db;
	
	
	public static function setUpBeforeClass()
	{	
	
		resetHandlers();
		$typ='dataBase';
		$name='atest';	
		$Code='Code';		
		$CodeVal='CodeVal';		
		$Student='Student';
		self::$db1=getBaseHandler ($typ, $name);
		initStateHandler ($Code		,$typ, $name);
		initStateHandler ($CodeVal	,$typ, $name);
		initStateHandler ($Student	,$typ, $name);
		
		$typ='fileBase';
		$name=$name.'_f';
		$Code='Codef';		
		$CodeVal='CodeValf';		
		$Student='Studentf';
		self::$db2=getBaseHandler ($typ, $name);
		initStateHandler ($Code		,$typ, $name);
		initStateHandler ($CodeVal	,$typ, $name);
		initStateHandler ($Student	,$typ, $name);
		
	}
	
	public function setTyp ($typ) 
	{
		if ($typ== 'SQL') {
			$this->db=self::$db1;
			$this->Student='Student';
			$this->Code='Code';
			$this->CodeVal='CodeVal';
			} 
		else {
			$this->db=self::$db2;
			$this->Student='Studentf';
			$this->Code='Codef';
			$this->CodeVal='CodeValf';
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
	
		// Ref -CodeVal
		$codeval = new Model($this->CodeVal);
		$this->assertNotNull($codeval);	
		
		$res= $codeval->deleteMod();
		$this->assertTrue($res);	
		
		$res = $codeval->addAttr('ValueName'); 
		$this->assertTrue($res);	
		
		$res = $codeval->setDflt('ValueName','Male'); //default
		$this->assertTrue($res);	
				
		$path='/'.$this->Code;
		$res = $codeval->addAttr('ValueOf',M_REF,$path);
		$this->assertTrue($res);	

		$res=$codeval->setMdtr('ValueOf',true); // Mdtr
		$this->assertTrue($res);

		$r = $codeval-> getErrLog ();
		$r->show();		

		$res = $codeval->saveMod();	
		$this->assertTrue($res);	

		$this->assertEquals($r->logSize(),0);	
		
		// CRef - Code
		$code = new Model($this->Code);
		$this->assertNotNull($code);	
	
		$res= $code->deleteMod();
		$this->assertTrue($res);	
		
		$res = $code->addAttr('CodeName'); 
		$this->assertTrue($res);	
		
		$res=$code->setBkey('CodeName',true);// unique
		$this->assertTrue($res);
		
		$path='/'.$this->CodeVal.'/ValueOf';
		$res = $code->addAttr('Values',M_CREF,$path);
		$this->assertTrue($res);	
		
		$res = $code->saveMod();	
		$this->assertTrue($res);	

		$r = $code-> getErrLog ();
		$this->assertEquals($r->logSize(),0);			
		
		$db->commit();
	}

	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testSaveMod
    */
	public function testNewCode($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		// Sexe
		
		$code = new Model($this->Code);
		$this->assertNotNull($code);	
		
		$this->assertTrue($code->isBkey('CodeName'));
		
		$this->assertNull($code->getDflt('CodeName'));
		
		$this->assertTrue($code->setVal('CodeName',null));
		
		$res = $code->setVal('CodeName','Sexe');
		$this->assertTrue($res);
		
//		$res = $code->setVal('CodeName','Sexe');
//		$this->assertTrue($res);
		
		$this->assertFalse($code->isOptl('Values'));
		
		$id = $code->save();
		$this->assertEquals($id,1);	

		$r = $code-> getErrLog ();
		$this->assertEquals($r->logSize(),0);	

		$db->commit();

		// check reload 
		
		$code = new Model($this->Code,$id);
		$this->assertNotNull($code);	
		
		$r = $code-> getErrLog ();
		$this->assertEquals($r->logSize(),0);	
		
		//  Male
		$codeval = new Model($this->CodeVal);
		$this->assertNotNull($codeval);	
		
		// check defaut and null 
		$res=$codeval->getVal('ValueName');
		$this->assertNull($res);

		$res=$codeval->getDflt('ValueName');
		$this->assertEquals($res,'Male');

		// check mandatory
		
		$res = $codeval->setVal('ValueName',$res);
		$this->assertTrue($res);
		
		$this->assertTrue($codeval->isOptl('ValueName'));
		
		$this->assertTrue($codeval->isMdtr('ValueOf'));
		
		$this->assertFalse($codeval->isOptl('ValueOf'));
		
		$res = $codeval->setVal('ValueOf',$id);
		$this->assertTrue($res);
		
		$id1= $codeval->save();
		$this->assertEquals($id1,1);	

		$r = $codeval-> getErrLog ();
		$this->assertEquals($r->logSize(),0);	

		$db->commit();
		
	}
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testNewCode
    */
		public function testErrors($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$code = new Model($this->Code);
		$this->assertNotNull($code);	
		$log = $code->getErrLog ();
		
		$res = $code->setVal('CodeName','Sexe');
		$this->assertFalse($res);
		$this->assertEquals($log->getLine(0),E_ERC018.':CodeName:Sexe');
		
						
		$codeval = new Model($this->CodeVal);
		$this->assertNotNull($codeval);	
		$log = $codeval->getErrLog ();
		
		$id1= $codeval->save();
		$this->assertEquals($id1,0);	

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(0),E_ERC019.':ValueOf');	
		
	    $res = $codeval->setVal('ValueOf',null);
		$id1= $codeval->save();

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(1),E_ERC019.':ValueOf');

		$res = $codeval->isBkey('notexists');

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(2),E_ERC002.':notexists');
	
		$res = $codeval->isMdtr('notexists');

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(3),E_ERC002.':notexists');
		
		$res = $codeval->isOptl('notexists');

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(4),E_ERC002.':notexists');
		
		$res = $codeval->setMdtr('notexists',false);

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(5),E_ERC002.':notexists');
		
		$res = $codeval->setDflt('notexists',false);

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(6),E_ERC002.':notexists');
		
		$res = $codeval->getDflt('notexists');

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(7),E_ERC002.':notexists');		

		$res = $codeval->setBkey('notexists',false);

		$r = $codeval-> getErrLog ();
		$this->assertEquals($log->getLine(5),E_ERC002.':notexists');
		
		$this->assertFalse($codeval->isOptl('id'));
		
	$db->commit();
	}
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testErrors
    */
	public function testDelAttr($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		
		$code = new Model($this->Code);
		$this->assertTrue($code->setBkey('CodeName',false));
		$this->assertTrue($code->setBkey('CodeName',true));

		
		$this->assertTrue($code->delAttr('CodeName'));
		
		$this->assertFalse($code->existsAttr('CodeName'));
		
		$codeval = new Model($this->CodeVal);
		
		$this->assertTrue($codeval->setMdtr('ValueOf',false));
		$this->assertTrue($codeval->setMdtr('ValueOf',true));
		
		$this->assertTrue($codeval->delAttr('ValueOf'));
		
		$this->assertFalse($codeval->existsAttr('ValueOf'));
		
		
		$this->assertTrue($codeval->delAttr('ValueName'));
		
		$db->commit();
	}
	
}

?>	