<?php
	
require_once("Model.php"); 
require_once("Handler.php"); 

class Model_Xref_Test extends PHPUnit_Framework_TestCase  
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
		$name='test';	
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
		
		$path='/'.$this->Code;
		$res = $codeval->addAttr('ValueOf',M_REF,$path);
		$this->assertTrue($res);	

		$res = $codeval->addAttr('Label');
		$this->assertTrue($res);	
		
		$res = $codeval->saveMod();	
		$this->assertTrue($res);	

		$r = $codeval-> getErrLog ();
		$r->show();
		$this->assertEquals($r->logSize(),0);	
		
		// CRef -Code
		$code = new Model($this->Code);
		$this->assertNotNull($code);	
	
		$res= $code->deleteMod();
		$this->assertTrue($res);	
		
		$res = $code->addAttr('CodeName');
		$this->assertTrue($res);	

		$path='/'.$this->CodeVal.'/ValueOf';
		$res = $code->addAttr('Values',M_CREF,$path);
		$this->assertTrue($res);	
		
		$res = $code->saveMod();	
		$this->assertTrue($res);	

		$r = $code-> getErrLog ();
		$this->assertEquals($r->logSize(),0);			
		
		// Code -Student 
		$student = new Model($this->Student);
		$this->assertNotNull($student);	

		$res= $student->deleteMod();
		$this->assertTrue($res);
		
		$res = $student->addAttr('Name');
		$this->assertTrue($res);			

		$path='/'.$this->Code.'/1/Values';
		$res = $student->addAttr('Sexe',M_CODE,$path);	
		$this->assertTrue($res);

		$res = $student->saveMod();	
		$this->assertTrue($res);	
	
		$r = $student-> getErrLog ();
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
		
		$res = $code->setVal('CodeName','Sexe');
		$this->assertTrue($res);
		
		$id = $code->save();
		$this->assertEquals($id,1);	

		$res = $code->getVal('Values');
		$this->assertEquals($res,[]);		

		$r = $code-> getErrLog ();
		$this->assertEquals($r->logSize(),0);

		$student = new Model($this->Student);
		$this->assertNotNull($student);	
		
		$res = $student->setVal('Sexe',2);
		$this->assertFalse($res);

		$db->commit();

		//  Male
		$codeval = new Model($this->CodeVal);
		$this->assertNotNull($codeval);	
		
		$res = $codeval->setVal('Label','Male');
		$this->assertTrue($res);
		
		$res = $codeval->setVal('ValueOf',$id);
		$this->assertTrue($res);

		$res = $codeval->getRef('ValueOf');
		$this->assertEquals($res,$code);	
		
		$res = $codeval->getRefMod('ValueOf');
		$this->assertEquals($res,$this->Code);		
				
		$id1= $codeval->save();
		$this->assertEquals($id1,1);	

		$res = $code->getVal('Values');
		$this->assertEquals($res,[$id1]);	

		$r = $codeval-> getErrLog ();
		$this->assertEquals($r->logSize(),0);	

		$db->commit();
		
		//Female
		
		$codeval = new Model($this->CodeVal);
		$this->assertNotNull($codeval);	
		
		$res = $codeval->setVal('Label','Female');
		$this->assertTrue($res);

		$res = $codeval->getRef('ValueOf');
		$this->assertNull($res);			

		$res = $codeval->setVal('ValueOf',$id);
		$this->assertTrue($res);
		
		$id2= $codeval->save();
		$this->assertEquals($id2,2);	

		$res = $code->getVal('Values');
		$this->assertEquals($res,[$id1,$id2]);	

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
	public function testNewCodeUse($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		// Student
		
		$student = new Model($this->Student);
		$this->assertNotNull($student);	
		
		$res = $student->getValues('Sexe');
		$this->assertEquals($res,[1,2]);	
		
		$res = $student->setVal('Name','Quoilin');
		$this->assertTrue($res);
		
		$res = $student->setVal('Sexe',null);
		$this->assertTrue($res);
		
		$res = $student->setVal('Sexe',2);
		$this->assertTrue($res);
		
		$res = $student->getCode('Sexe',2);
		
		$codeval = new Model($this->CodeVal,2);
		$codeval->protect('ValueOf');
				
		$this->assertEquals($codeval,$res);

		
		$id= $student->save();
		$this->assertEquals($id,1);

		$r = $student-> getErrLog ();
		$this->assertEquals($r->logSize(),0);	

		$db->commit();
	}
	
	/**
     * @dataProvider Provider1
     *	
	/**
    * @depends testNewCodeUse
    */
		public function testErrors($typ) 
	{
		
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$student = new Model($this->Student);
		$this->assertNotNull($student);	
		$log = $student->getErrLog ();
		
		$res = $student->addAttr('xxx',M_CODE);
		$this->assertFalse($res);	
		$this->assertEquals($log->getLine(0),E_ERC008.':xxx:'.M_CODE);
		
		$bb = new Model('bb');
		$this->assertNotNull($bb);	
		$logbb = $bb->getErrLog ();
		
		$res = $bb->addAttr('xxx',M_CODE,'xxx');
		$this->assertFalse($res);	
		$this->assertEquals($logbb->getLine(0),E_ERC014.':xxx:'.M_CODE);
		
		$res = $student->addAttr('xxx',M_CODE,'xx');
		$this->assertFalse($res);	
		$this->assertEquals($log->getLine(1),E_ERC020.':xxx:xx');
		

		$code = new Model($this->Code);
		$this->assertNotNull($code);	
		$log = $code->getErrLog ();
		
		$res = $code->setVal('Values','xx');
		$this->assertFalse($res);
		$this->assertEquals($log->getLine(0),E_ERC013.':Values');
						
		$codeval = new Model($this->CodeVal);
		$this->assertNotNull($codeval);	
		$log = $codeval->getErrLog ();
		
		$res = $codeval->setVal('ValueOf',1000);
		$this->assertFalse($res);
		$this->assertEquals($log->getLine(0),E_ERC007.':'.$this->Code.':1000');
		
		$log = $student->getErrLog ();
		$res = $student->setVal('Sexe',1000);
		$this->assertFalse($res);
		$this->assertEquals($log->getLine(2),E_ERC016.':Sexe:1000');
		
		$n = 2;
		
		$n++;
		$res = $student->getRefMod('notexists');
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC002.':notexists');

		$n++;
		$res = $student->getRefMod('Sexe');
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC026.':Sexe');		

		$n++;
		$res = $student->getRef('notexists');
		$this->assertNull($res);
		$this->assertEquals($log->getLine($n),E_ERC002.':notexists');

		$n++;
		$res = $student->getValues('notexists');
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC002.':notexists');

		$n++;
		$res = $student->getValues('Name');
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC015.':Name:'.M_STRING);			
	
		$n++;
		$res = $student->getCref('notexists',1);
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC002.':notexists');

		$n++;
		$res = $student->getCref('Sexe',1);
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC027.':Sexe');		
	
		$n++;
		$res = $student->getCode('notexists',1);
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC002.':notexists');

		$n++;
		$res = $student->getCode('Name',1);
		$this->assertFalse($res);
		$this->assertEquals($log->getLine($n),E_ERC028.':Name');
		
		$db->commit();
	}
}

?>	