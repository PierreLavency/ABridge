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

	public function testPath($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
	
		$mod = new Model($this->CName);	
		$res= $mod->deleteMod();

		$res = $mod->addAttr('Name');
		$res = $mod->addAttr('Ref',M_REF,'/'.$this->CName);
		$res = $mod->addAttr('CRef',M_CREF,'/'.$this->CName.'/Ref');
		
		$res = $mod->saveMod();	

		$res=$mod->setVal('Name','Lavency');			

		$id = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);

		$mod = new Model($this->CName);	
		$res=$mod->setVal('Name','Quoilin');
		$res=$mod->setVal('Ref',$id);

		$id1 = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);
	
		$mod = new Model($this->CName);	
		$res=$mod->setVal('Name','Lories');
		$res=$mod->setVal('Ref',$id1);

		$id2 = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);

		$mod = new Model($this->CName);	
		$res=$mod->setVal('Name','Arnould');
		$res=$mod->setVal('Ref',$id2);

		$id3 = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);
		$db->commit();

		// constructors 
		$p = new Path();
		$this->assertNotNull($p);
		$this->assertEquals($p->getPath(),$p->RootPath().$p->getDefaultPath());

		$_SERVER['PATH_INFO']=$p->getDefaultPath();
		
		$p = new Path();
		$this->assertEquals($p->getPath(),$p->RootPath().$p->getDefaultPath());
		
		// creat path 
		
		$path='/'.$this->CName;
		
		$p1 = new Path($path);
		$this->assertNotNull($p1);		
		
		$mod = $p1->getObj();
		$this->assertNotNull($mod);
		$this->assertEquals($mod->getModName(),$this->CName);
		
		$this->assertTrue($p1->isCreatPath());
		$this->assertEquals($p1->getCreaPath(),$p1->getPath());		
		
		$pathr=$p1->getPath();
		$this->assertEquals($pathr,$p1->RootPath().$path);
		
		$pathr=$p1->getObjPath();
		$this->assertEquals($pathr,$p1->RootPath().$p1->getDefaultPath());
		
		
		// obj path
		
		$path=$path.'/1';
		$p2 = new Path($path);
		$this->assertNotNull($p2);
		
		$mod = $p2->getObj();
		$this->assertNotNull($mod);
		$this->assertEquals($mod->getModName(),$this->CName);
		$this->assertEquals($mod->getId(),1);
			
		$this->assertFalse($p2->isCreatPath());	
		
		$pathr=$p2->getPath();
		$this->assertEquals($pathr,$p2->RootPath().$path);
		
		$pathr=$p2->getObjPath();
		$this->assertEquals($pathr,$p2->RootPath().$path);
		
		$this->assertEquals($p2->getCreaPath(),$p1->getPath());
		
		// push pop 
		$this->assertTrue($p1->pushId('1'));
		
		$this->assertEquals($p2->getPath(),$p1->getPath());

		$this->assertTrue($p1->push($this->CName,'1'));		
		$this->assertTrue($p1->pop());
		
		$this->assertEquals($p2->getPath(),$p1->getPath());
		
		$this->assertTrue($p1->pop());
		$this->assertEquals($p1->getPath(),$p1->RootPath().$p1->getDefaultPath());
		
		// refpath
		
		$rpath='/'.$this->CName.'/'.$id.'/CRef/'.$id1;
		$p3 = new Path($rpath);
		$this->assertNotNull($p3);
		$obj = $p3->getObj();
			
		$path = $rpath.'/CRef/'.$id2.'/CRef/'.$id3;
		$p4 = new Path($path);
		$this->assertNotNull($p4);
		$opath = $p4->getRefPath($obj);
		$this->assertEquals($p4->rootPath().$rpath,$opath);
		
		$p5 = new Path();
		$opath = $p5->getRefPath($obj);
		$this->assertEquals($p5->rootPath().'/'.$obj->getModName().'/'.$obj->getId(),$opath);
		
		// get obj
		$rpath='/'.$this->CName.'/'.$id.'/CRef';
		$p6 = new Path($rpath);
		$this->assertNotNull($p6);
		$obj=$p6->getObj();
		$this->assertEquals($obj->getId(),0);
		
		$res = $p6->getObjPath();
		$this->assertEquals($res,$p6->rootPath().'/'.$this->CName.'/'.$id);
		
	}
	/**
     * @dataProvider Provider1
     */
		public function testPathErr($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$pathStrg=1;
		try {$x=new Path($pathStrg);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC036.':'.$pathStrg);
		
		$pathStrg='/';
		try {$x=new Path($pathStrg);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC036.':'.$pathStrg);
		
		$pathStrg='/$/1';
		try {$x=new Path($pathStrg);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC036.':'.$pathStrg.':0');

		$pathStrg='/a/$';
		try {$x=new Path($pathStrg);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC036.':'.$pathStrg.':1');

		$pathStrg='/a/1/$';
		try {$x=new Path($pathStrg);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC036.':'.$pathStrg.':2');
		
		$path='/'.$this->CName;
		$p1 = new Path($path);
		$this->assertNotNull($p1);
		
		try {$x=$p1->push($this->CName,1);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC035);
		
		try {$x=$p1->pop();} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC035);
		
		$path='/'.$this->CName.'/1';
		$p1 = new Path($path);
		$this->assertNotNull($p1);
		
		try {$x=$p1->pushId(1);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC037);
	}
		
}
