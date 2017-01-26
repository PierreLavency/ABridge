<?php
	
require_once("Model.php"); 
require_once("Path.php"); 
require_once("ViewConstant.php"); 

class Path_Test extends PHPUnit_Framework_TestCase  
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

	public function testPath($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$mod = new Model($this->CUser);	
		$res = $mod->deleteMod();
		$res = $mod->addAttr('Name',M_STRING);
		$res = $mod->addAttr($this->CName,M_CREF,'/'.$this->CName.'/'.$this->CUser);		
		$res = $mod->saveMod();
		$res = $mod->setVal('Name','User1');	
		$u1  = $mod->save();			
		$mod = new Model($this->CUser);	
		$res = $mod->setVal('Name','User2');	
		$u2  = $mod->save();		
		
		$mod = new Model($this->CName);	
		$res= $mod->deleteMod();

		$res = $mod->addAttr('Name',M_STRING);
		$res = $mod->addAttr($this->CUser,M_REF,'/'.$this->CUser);
		$res = $mod->addAttr('Ref',M_REF,'/'.$this->CName);
		$res = $mod->addAttr('CRef',M_CREF,'/'.$this->CName.'/Ref');
		
		$res = $mod->saveMod();	

		$res=$mod->setVal('Name','Lavency');			
		$res=$mod->setVal($this->CUser,$u1);
		$id = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);

		$mod = new Model($this->CName);	
		$res=$mod->setVal('Name','Quoilin');
		$res=$mod->setVal('Ref',$id);
		$res=$mod->setVal($this->CUser,$u1);
		
		$id1 = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);
	
		$mod = new Model($this->CName);	
		$res=$mod->setVal('Name','Lories');
		$res=$mod->setVal('Ref',$id1);
		$res=$mod->setVal($this->CUser,$u1);
		
		$id2 = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);

		$mod = new Model($this->CName);	
		$res=$mod->setVal('Name','Arnould');
		$res=$mod->setVal('Ref',$id2);
		$res=$mod->setVal($this->CUser,$u2);
		
		$id3 = $mod->save();	
		$r = $mod-> getErrLog ();
		$this->assertEquals($r->logSize(),0);
		$db->commit();

		// constructors 
		$p = new Path();
		$p->setHome('/');

		$this->assertNotNull($p);
		$this->assertEquals($p->getPath(),$p->getHomePath());

		$_SERVER['PATH_INFO']='/';
		
		$p = new Path();
		$p->setHome('/');
		$this->assertEquals($p->getPath(),$p->getHomePath());
		
		// creat path 
		
		$path1='/'.$this->CName;
		
		$p1 = new Path($path1);
		$p1->setHome('/');
		$this->assertNotNull($p1);		
		
		$mod = $p1->getObj();
		$this->assertNotNull($mod);
		$this->assertEquals($mod->getModName(),$this->CName);
		
		$this->assertTrue($p1->isCreatPath());
		$this->assertFalse($p1->isObjPath());
		$this->assertEquals($p1->getPath(),$p1->prfxPath($path1));			
		$this->assertEquals($p1->getCreaPath(),$p1->getPath());				
		$this->assertEquals($p1->getObjPath(),$p1->getHomePath());
	
		
		// obj path
		
		$path2=$path1.'/1';
		$p2 = new Path($path2);
		$p2->setHome('/');
		$this->assertNotNull($p2);
		
		$mod = $p2->getObj();
		$this->assertNotNull($mod);
		$this->assertEquals($mod->getModName(),$this->CName);
		$this->assertEquals($mod->getId(),1);
			
		$this->assertFalse($p2->isCreatPath());	
		$this->assertTrue($p2->isObjPath());				
		$this->assertEquals($p2->getPath(),$p2->prfxPath($path2));		
		$this->assertEquals($p2->getObjPath(),$p2->getPath());		
		$this->assertEquals($p2->getCreaPath(),$p1->getPath());
		
		// push pop
		
		$this->assertTrue($p1->pushId('1'));		
		$this->assertEquals($p2->getPath(),$p1->getPath());

		$this->assertTrue($p1->push('CRef','2'));		
		$this->assertTrue($p1->pop());		
		$this->assertEquals($p2->getPath(),$p1->getPath());
		
		$this->assertTrue($p1->popId());
		$this->assertEquals($p1->getPath(),$p1->prfxPath($path1));

		$this->assertTrue($p2->pop());		
		$this->assertEquals($p2->getPath(),$p2->getHomePath());
		
				
		// refpath
		
		$rpath='/'.$this->CName.'/'.$id.'/CRef/'.$id1;
		$p3 = new Path($rpath);
		$p3->setHome('/');
		$obj = $p3->getObj();
			
		$path = $rpath.'/CRef/'.$id2.'/CRef/'.$id3;
		$p4 = new Path($path);
		$p4->setHome('/');
		$opath = $p4->getRefPath($obj);
		$this->assertEquals($p4->prfxPath($rpath),$opath);
		
		$p5 = new Path();
		$p5->setHome('/');
		$opath = $p5->getRefPath($obj);
		$this->assertEquals($p5->prfxPath('/'.$obj->getModName().'/'.$obj->getId()),$opath);
		
		// get obj
		$rpath='/'.$this->CName.'/'.$id.'/CRef';
		$p6 = new Path($rpath);
		$p6->setHome('/');
		$obj=$p6->getObj();
		$this->assertEquals($obj->getId(),0);
		
		$res = $p6->getObjPath();
		$this->assertEquals($res,$p6->prfxPath('/'.$this->CName.'/'.$id));
		
	}
	
	/**
     * @dataProvider Provider1
     */
	public function testHome($typ) {

		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$rpath='/'.$this->CName.'/1/CRef/2';
		$p = new Path($rpath);
		$p->setHome('/'.$this->CUser.'/1');
		
		$obj = $p->getObj();
		$this->assertNotNull($obj);
		
		$this->assertTrue($obj->isProtected($this->CUser));
		$res = $p->getActionPath(V_S_UPDT);
		$this->assertNotNull($res);		
		$res=$p->getCrefPath('Cref',V_S_CREA);
		$this->assertNotNull($res);
		$x = new Model ($this->CName,1);
		$res= $p->getRefPath($x);
		$this->assertEquals($p->prfxPath('/'.$this->CName.'/1'),$res);
		$x = new Model ($this->CName,3);
		$res= $p->getRefPath($x);
		$this->assertEquals($p->prfxPath('/'.$this->CName.'/3'),$res);
		$x = new Model ($this->CName,4);
		$res= $p->getRefPath($x);
		$this->assertNull($res);		
		
		$rpath= '/'.$this->CName.'/3/CRef/4';
		$p = new Path($rpath);
		$p->setHome('/'.$this->CUser.'/1');

		$obj = $p->getObj();
		$this->assertNotNull($obj);
		$this->assertTrue($obj->isProtected($this->CUser));
		$res = $p->getActionPath(V_S_UPDT);
		$this->assertNull($res);
		$res=$p->getCrefPath('Cref',V_S_CREA);
		$this->assertNull($res);			

		$rpath = '/'.$this->CName.'/1/CRef';
		$p = new Path($rpath);
		$p->setHome('/'.$this->CUser.'/1');
		$obj = $p->getObj();
		
		$this->assertNotNull($obj);
		$this->assertTrue($obj->isProtected($this->CUser));
		$this->assertEquals(1,$obj->getVal($this->CUser));
		
		$rpath= '/'.$this->CName.'/4';
		$p = new Path($rpath);
		$p->setHome('/'.$this->CUser.'/1');

		$obj = $p->getObj();
		$this->assertNull($obj);
		
	}
	
	/**
     * @dataProvider Provider1
     */
	public function testRoot($typ) 
	{
		$p =  new Path('/');
		$p->setHome('/');
		$this->assertNotNull($p);
		
		$this->assertFalse($p->isCreatPath());
		$this->assertFalse($p->isObjPath());

		$this->assertTrue($p->pop());
		
		$this->assertNull($p->getObj());
		
//		$this->assertEquals($p->getRefPath('X',1),$p->prfxPath('/X/1'));
		
		try {$x=$p->pushId(1);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC038);

		try {$x=$p->popId();} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC038);
	
		
		try {$x=$p->getObjPath();} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC038);

		try {$x=$p->getCreaPath();} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC038);
		
		try {$x=$p->getactionPath(V_S_DELT);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC038);
		
//		$this->assertTrue($p->push('X',1));
//		$this->assertEquals($p->prfxPath('/X/1'),$p->getPath());
				
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
		$this->assertEquals($x->getPath(),$x->prfxPath($pathStrg));
		
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
		$p1->setHome('/');
		$this->assertNotNull($p1);
		
		try {$x=$p1->push($this->CName,1);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC035);
		
		try {$x=$p1->pop();} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC035);

		try {$x=$p1->popId();} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC035);

		try {$x=$p1->getActionPath( V_S_DELT);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC037.':'. V_S_DELT);
		
		$path='/'.$this->CName.'/1';
		$p1 = new Path($path);
		$this->assertNotNull($p1);
		
		try {$x=$p1->pushId(1);} catch (Exception $e) {$r= $e->getMessage();}
		$this->assertEquals($r, E_ERC037);
		
		
	}
		
}
