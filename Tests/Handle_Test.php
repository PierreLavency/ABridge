<?php

require_once("Handler.php"); 
require_once("Handle.php"); 
require_once("ViewConstant.php");

class Handle_Test extends PHPUnit_Framework_TestCase  
{
	protected static $db1;
	protected static $db2;

	protected $CName='Example';
	protected $CUser='User';
    protected $CCode='Code';
	
	protected $db;
	
	
	public static function setUpBeforeClass()
	{	
	
		resetHandlers();
	
		$typ='dataBase';
		$CName='Example';
		$CUser='User';
		$CCode='Code';		
		$name = 'test';
		self::$db1=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		initStateHandler ($CUser	,$typ, $name);
        initStateHandler ($CCode    ,$typ, $name); 
		
		$typ='fileBase';
		$name=$name.'_f';
		$CName='Examplef';
		$CUser='Userf';
		$CCode='Codef';
		
		self::$db2=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		initStateHandler ($CUser	,$typ, $name);
        initStateHandler ($CCode    ,$typ, $name);
		
	}
	
	public function setTyp($typ) 
	{
		if ($typ== 'SQL') {
			$this->db=self::$db1;
			$this->CName='Example';
			$this->CUser='User';
			$this->CCode='Code';			
			} 
		else {
			$this->db=self::$db2;
			$this->CName='Examplef';
			$this->CUser='Userf';
			$this->CCode='Codef';			
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

        $mod = new Model($this->CCode); 
        $res = $mod->deleteMod();		
		$res = $mod->addAttr('Ref', M_REF, '/'.$this->CCode);
        $res = $mod->addAttr('CRef', M_CREF, '/'.$this->CCode.'/Ref');
        $res = $mod->saveMod();
        $r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);
		
		$c1 = $mod->save();
		$r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);
		
		$mod = new Model($this->CCode); 
        $res=$mod->setVal('Ref',$c1);        
		$c2 = $mod->save();
		$r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);
		
		$mod = new Model($this->CCode); 
        $res=$mod->setVal('Ref',$c1);        
		$c3 = $mod->save();
        $r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);

		
        $mod = new Model($this->CUser); 
        $res = $mod->deleteMod();
        $res = $mod->addAttr($this->CName,M_CREF,'/'.$this->CName.'/'.$this->CUser);		
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
		$res = $mod->addAttr('Code',M_CODE,'/'.$this->CCode.'/'.$c1.'/CRef');
        $res = $mod->addAttr($this->CUser,M_REF,'/'.$this->CUser);
        $res = $mod->setDflt($this->CUser,1);
        
        $res = $mod->saveMod();

        $res=$mod->setVal('Code',$c2);             
        $res=$mod->setVal($this->CUser,$u1); 
		
        $id1 = $mod->save();
        $obj1 = $mod;       
        $r = $mod-> getErrLog ();
        $r->show();
        $this->assertEquals($r->logSize(),0);
        
        $mod = new Model($this->CName);
        $res=$mod->setVal($this->CUser,$u2);
        $res=$mod->setVal('Code',$c2);              
        $res=$mod->setVal('Ref',$id1);  
        $id2 = $mod->save();    
        $r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);

        $mod = new Model($this->CName);
        $res=$mod->setVal($this->CUser,$u1); 
        $res=$mod->setVal('Code',$c2);             		
        $res=$mod->setVal('Ref',$id2);  
        $id3 = $mod->save();    
        $r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);

        $mod = new Model($this->CName);
        $res=$mod->setVal($this->CUser,$u1); 
        $res=$mod->setVal('Code',$c2);             		
        $res=$mod->setVal('Ref',$id3);  
        $id4 = $mod->save();    
        $r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);    
		
		
		
		
		$path1 = '/'.$this->CName.'/1';
		$r = new Request($path1,V_S_READ);
		$apath1 = $r-> prfxPath($path1);

		$ho = new Home('/'); 
		
		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);
		$this->assertEquals($apath1, $h1->getPath());
		$this->assertEquals($path1,  $h1->getRPath());
		
		$act_path = $h1->getActionPath(V_S_UPDT);
		$e_path = $r->prfxPath($path1).'?View='.V_S_UPDT;
		$this->assertEquals($e_path,  $act_path);	

		$act_path = $h1->getClassPath($this->CName,V_S_CREA);
		$e_path = $r->prfxPath('/'.$this->CName).'?View='.V_S_CREA;
		$this->assertEquals($e_path,  $act_path);	

		$act_path = $h1->getCrefPath('CRef',V_S_CREA);
		$e_path = $r->prfxPath($path1.'/CRef').'?View='.V_S_CREA;
		$this->assertEquals($e_path,  $act_path);
 
		$this->assertEquals(2,$h1->getCode('Code',2)->getId());		
		$this->assertEquals('/'.$this->CCode.'/2',$h1->getCode('Code',2)->getRPath());
		
		$h2 = $h1->getCref('CRef',$id2);
		
		$path2=$path1.'/CRef/2';
		$this->assertEquals($path2,  $h2->getRPath());

		$act_path = $h2->getCrefPath('CRef',V_S_CREA);
		$this->assertNull($act_path);
		
		$act_path = $h2->getClassPath($this->CName,V_S_CREA);
		$this->assertNull($act_path);
		
		$act_path = $h2->getActionPath(V_S_UPDT);
		$this->assertNull($act_path);			
		
		$h3 = $h2->getCref('CRef',$id3);
		$h4 = $h3->getRef('Ref');
		$e_path = '/'.$this->CName.'/2';
		$this->assertEquals($e_path,  $h4->getRPath());

		$r = new Request($path2,V_S_READ);
		$h2 = new Handle($r,$ho);
		$h3 = $h2->getCref('CRef',$id3);
		$h4 = $h3->getRef('Ref');
		$this->assertEquals($path2,  $h4->getRPath());

		$path3 = '/'.$this->CName;
		$r = new Request($path3,V_S_SLCT);		
		$h5 = new Handle($r,$ho);

		$act_path = $h5->getCrefPath('CRef',V_S_CREA);
		$this->assertNull($act_path);
		
		$h6 = $h5->getObjId(1);
		$act_path = $h6->getRPath();
		$this->assertEquals($path1,  $act_path);
			
		$db->commit();
	}
	
	/**
     * @dataProvider Provider1
     */
	/**
    * @depends testRoot
    */
	public function testHomeObj($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$path1 = '/'.$this->CName.'/1';
		$r = new Request($path1,V_S_READ);

		$ho = new Home('/'.$this->CUser.'/1'); 

		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);
		$this->assertEquals($path1,  $h1->getRPath());

		$res = $h1->getRef('Ref');
		$this->assertNull($res);
		
		$act_path = $h1->getActionPath('x');
		$this->assertNull($act_path);
			
		$act_path = $h1->getActionPath(V_S_UPDT);
		$e_path = $r->prfxPath($path1).'?View='.V_S_UPDT;
		$this->assertEquals($e_path,  $act_path);			

		$act_path = $h1->getClassPath($this->CName,V_S_CREA);
		$e_path = $r->prfxPath('/'.$this->CName).'?View='.V_S_CREA;
		$this->assertEquals($e_path,  $act_path);
		
        $this->assertEquals(2,$h1->getCode('Code',2)->getId());		
		$this->assertNull($h1->getCode('Code',2)->getPath());
		
		$h2 = $h1->getCref('CRef',2);		
		$path2=$path1.'/CRef/2';
		$this->assertEquals($path2,  $h2->getRPath());

		$act_path = $h2->getCrefPath('CRef',V_S_CREA);
		$this->assertNull($act_path);
		
		$h3 = $h2->getCref('CRef',3);
		$h4 = $h3->getRef('Ref');
		$this->assertNull($h4->getRPath());

		$r = new Request($path2,V_S_READ);
		$h2 = new Handle($r,$ho);
		
		$act_path = $h2->getCrefPath('CRef',V_S_CREA);
		$this->assertNull($act_path);	
		
		$act_path = $h2->getClassPath($this->CUser,V_S_CREA);
		$this->assertNull($act_path);	

		$path3 = $path2.'/CRef/3/CRef';
		$r = new Request($path3,V_S_CREA);		
		$h5 = new Handle($r,$ho);
		$act_path=$h5->getRPath();
		$this->assertEquals($path3,  $act_path);

		$path4 = '/'.$this->CName.'/4';
		$r = new Request($path4,V_S_READ);		
		$h6 = new Handle($r,$ho);

		$h7 = $h6->getRef('Ref');
		$this->assertEquals('/'.$this->CName.'/3',  $h7->getRPath());

		$path5 = '/'.$this->CName.'/3';
		$r = new Request($path5,V_S_READ);		
		$h8 = new Handle($r,$ho);
		$h9 = $h8->getRef('Ref');
		$this->assertNull($h9->getPath());
		
		
		$db->commit();
	}
	
	/**
     * @dataProvider Provider1
     */
	/**
    * @depends testHomeObj
    */
	public function testHome($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$path1 = '/';
		$r = new Request($path1,V_S_READ);

		$ho = new Home('/'.$this->CUser.'/1'); 

		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);	

		$h2 = $h1->getCref($this->CName,1);		
		$path2='/'.$this->CName.'/1';
		$this->assertEquals($path2,  $h2->getRPath());

		$path3 = '/'.$this->CName;
		$r = new Request($path3,V_S_CREA);
		$ho = new Home('/'.$this->CUser.'/1'); 

		$h3 = new Handle($r,$ho);
		$this->assertEquals($path3,  $h3->getRPath());		

		$path4 = '/'.$this->CUser;
		$r = new Request($path4,V_S_CREA);
		$ho = new Home('/'.$this->CUser.'/1'); 

		try {$h4 = new Handle($r,$ho);} catch (Exception $e) {$res=$e->getMessage();}
		$this->assertEquals($res,E_ERC049.':'.V_S_CREA);

		$path1 = '/';
		$r = new Request($path1,V_S_READ);
		$ho = new Home('/');

		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);	
		
		$act_path = $h1->getActionPath(V_S_READ);

		$this->assertNull($act_path);			
		
		$db->commit();
	}		
}
