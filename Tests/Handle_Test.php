<?php

require_once 'Handler.php'; 
require_once 'Handle.php'; 
require_once 'SessionHdl.php';
require_once 'CstMode.php';

class Handle_Test extends PHPUnit_Framework_TestCase  
{
	protected static $db1;
	protected static $db2;

	protected $CName;
	protected $CUser;
    protected $CCode; 
	
	protected $db;
	
	
	public static function setUpBeforeClass()
	{	
	
		resetHandlers();
	
		$typ='dataBase';
        $CName=get_called_class().'_1';
        $CUser=get_called_class().'_2';
		$CCode=get_called_class().'_3';
		$name = 'test';
		self::$db1=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		initStateHandler ($CUser	,$typ, $name);
        initStateHandler ($CCode    ,$typ, $name); 
		
		$typ='fileBase';
		$name=$name.'_f';
        $CName=get_called_class().'_f_1';
        $CUser=get_called_class().'_f_2';
		$CCode=get_called_class().'_f_3';
		
		self::$db2=getBaseHandler ($typ, $name);
		initStateHandler ($CName	,$typ, $name);
		initStateHandler ($CUser	,$typ, $name);
        initStateHandler ($CCode    ,$typ, $name);
		
	}
	
    public function setTyp($typ) 
    {
        if ($typ== 'SQL') {
            $this->db=self::$db1;
            $this->CName=get_called_class().'_1';
            $this->CUser=get_called_class().'_2';
			$this->CCode=get_called_class().'_3';
            } 
        else {
            $this->db=self::$db2;
            $this->CName=get_called_class().'_f_1';
            $this->CUser=get_called_class().'_f_2';
			$this->CCode=get_called_class().'_f_3';
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

		$ho = new sessionHdl();
		
// code
        $mod = new Model($this->CCode); 
        $res = $mod->deleteMod();		
        $res = $mod->saveMod();
//        $r = $mod-> getErrLog ();
//		  $r->show();
        $this->assertfalse($mod->isErr());

		$hc1 = new Handle('/'.$this->CCode,V_S_CREA,$ho);
		$hc1->save();
		$this->assertfalse($hc1->isErr());
		
		$hc2 = new Handle('/'.$this->CCode,V_S_CREA,$ho);
		$hc2->save();
		$this->assertfalse($hc2->isErr());


// User		
        $mod = new Model($this->CUser); 
        $res = $mod->deleteMod();
        $res = $mod->saveMod();
        $this->assertfalse($mod->isErr());

		$hu1 = new Handle('/'.$this->CUser,V_S_CREA,$ho);
		$hu1->save();
		$this->assertfalse($hu1->isErr());
		
		$hu2 = new Handle('/'.$this->CUser,V_S_CREA,$ho);
		$hu2->save();
		$this->assertfalse($hu2->isErr());


// Class
        
        $mod = new Model($this->CName); 
        $res= $mod->deleteMod();
        $res = $mod->addAttr('Ref',M_REF,		'/'.$this->CName);
        $res = $mod->addAttr('CRef',M_CREF,		'/'.$this->CName.'/Ref');
		$res = $mod->addAttr('Code',M_CODE,		'/'.$this->CCode);
        $res = $mod->addAttr($this->CUser,M_REF,'/'.$this->CUser);
        $res = $mod->setDflt($this->CUser,1);
        $res = $mod->saveMod();
        $this->assertfalse($mod->isErr());			
		
        $ho1 = new Handle('/'.$this->CName,V_S_CREA,$ho); 		   
        $res=$ho1->setVal($this->CUser,	$hu1->getId()); 
        $res=$ho1->setVal('Code',		$hc2->getId());        		
        $id1 = $ho1->save();
        $obj1 = $ho1;       
        $this->assertfalse($ho1->isErr());
        
        $ho2 = new Handle('/'.$this->CName,V_S_CREA,$ho); 	
        $res=$ho2->setVal($this->CUser,	$hu2->getId());
        $res=$ho2->setVal('Code',		$hc2->getid());              
        $res=$ho2->setVal('Ref',		$ho1->getId());  
        $id2 = $ho2->save();    
        $this->assertfalse($ho2->isErr());

        $ho3 = new Handle('/'.$this->CName,V_S_CREA,$ho); 	
        $res=$ho3->setVal($this->CUser,	$hu1->getId()); 
        $res=$ho3->setVal('Code',		$hc2->getId());             		
        $res=$ho3->setVal('Ref',		$ho2->getId());  
        $id3 = $ho3->save();    
		$this->assertfalse($ho3->isErr());

        $ho4 = new Handle('/'.$this->CName,V_S_CREA,$ho); 	
        $res=$ho4->setVal($this->CUser,	$hu1->getId()); 
        $res=$ho4->setVal('Code',		$hc2->getId());             		
        $res=$ho4->setVal('Ref',		$ho3->getId());  
        $id4 = $ho4->save();    
		$this->assertfalse($ho4->isErr());
				
// 		
		$path0= '/'.$this->CName;				
		$path1 = $path0.'/'.$ho1->getId();
		$r = new Request($path1,V_S_READ);
	
		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);
		$this->assertTrue($h1->isMain());
		$this->assertEquals($path1,  $h1->getRPath());		
		$this->assertNull($h1->getRef('Ref'));
				
		$path2=$path1.'/CRef/'.$ho2->getId();
		$h2 = $h1->getCref('CRef',$ho2->getId());
		$this->assertNotNull($h2);
		$this->assertFalse($h2->isMain());		
		$this->assertEquals($ho2->getId(),$h2->getId());
		$this->assertEquals($path2,$h2->getRpath());		
		$this->assertTrue($h2->isMainRef('Ref'));
		
		$path3=$path2.'/CRef/'.$ho3->getId();
		$h3 = new Handle($path3,V_S_READ,$ho);
		$this->assertNotNull($h3);
		$this->assertEquals($ho3->getId(),$h3->getId());
		$this->assertEquals($path3,$h3->getRpath());		
		$this->assertFalse($h3->isMainRef('Ref'));
		
		$h2r = $h3->getRef('Ref');
		$this->assertEquals($h2->getId(),$h2r->getId());
		$this->assertEquals($h2->getRPath(),$h2r->getRPath());
		
		$h = new Handle($path0,V_S_CREA,$ho);
		$url= $h->getClassPath($this->CName, V_S_CREA);
		$urle= $h->getUrl();
		$this->assertEquals($urle,$url);		
		$url= $h1->getActionPath(V_S_CREA);
		$this->assertEquals($urle,$url);
		
		$h = new Handle($path0,V_S_SLCT,$ho);
		$h=$h->getObjId($h1->getId());
		$this->assertEquals($h1->getUrl(),$h->getUrl());

		$url = $h1->getCrefPath('CRef',V_S_CREA);
		$this->assertEquals($h1->getUrl().'/CRef?Action='.V_S_CREA,$url);
		
		$id = $h1->getVal('Code');
		$hc1r=$h1->getCode('Code',$id);
		$this->assertEquals($hc2->getUrl(),$hc1r->getUrl());

		
		$h=$h1;
        $this->assertFalse($h->nullObj());
        $this->assertEquals($this->CName,$h->getModName());
		$this->assertEquals($this->CName,$h->getModCref('CRef'));

        $aList = $h->getAttrList();
		$id= $h->getId();
        $this->assertEquals(8, count($aList));
        $this->assertEquals(M_REF, $h->getTyp('Ref'));
        $this->assertEquals($hu1->getId(), $h->getDflt($this->CUser));  
        $this->assertEquals($id, $h->getVal('id'));
        $this->assertEquals(2, count($h->getValues($this->CUser)));
		$this->assertFalse($h->isProtected($this->CUser));
        $this->assertFalse($h->isMdtr($this->CUser));
        $this->assertFalse($h->isEval($this->CUser));
        $this->assertTrue($h->isModif($this->CUser));
        $this->assertTrue($h->isSelect($this->CUser));
        $this->assertEquals($id,$h->save());
        $this->assertFalse($h->isErr());
        $this->assertEquals(0,$h->getErrLog()->logSize());
        $this->assertTrue($h->setCriteria(['Ref'], ['='], [1]));
        $this->assertEquals(1,count($h->select()));
		
		
		$h = new Handle('/',V_S_READ,$ho);
		$this->assertTrue($h->nullObj());

		$req = new Request('/'.$this->CName.'/'.$h1->getId().'/CRef',V_S_CREA);
		$h = new Handle($req, $ho);
		$id = $h ->save();
		$this->assertNotNull($id);
		$res = $h->delet();
		$this->assertTrue($res);
		$this->assertEquals(0,$h->getId());
		
		$db->commit();
	}
	
	/**
     * @dataProvider Provider1
     */
	/**
    * @depends testRoot
    */
	public function estHomeObj($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();
		
		$path1 = '/'.$this->CName.'/1';
		$r = new Request($path1,V_S_READ);

//		$ho = new Home('/'.$this->CUser.'/1'); 
		$ho = new sessionHdl();

		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);
		$this->assertEquals($path1,  $h1->getRPath());

		$res = $h1->getRef('Ref');
		$this->assertNull($res);
		
		$act_path = $h1->getActionPath('x');
		$this->assertNull($act_path);
			
		$act_path = $h1->getActionPath(V_S_UPDT);
		$this->assertNotNull($act_path);			

		$act_path = $h1->getClassPath($this->CName,V_S_CREA);
		$this->assertNotNull($act_path);
		
        $this->assertEquals(2,$h1->getCode('Code',2)->getId());		
		$this->assertNull($h1->getCode('Code',2)->getPath());
		
		$h2 = $h1->getCref('CRef',2);		
		$path2=$path1.'/CRef/2';
		$this->assertEquals($path2,  $h2->getRPath());

		$act_path = $h2->getCrefPath('CRef',V_S_CREA);
		$this->assertNull($act_path);
		
		$h3 = $h2->getCref('CRef',3);
		$h4 = $h3->getRef('Ref');
//		$this->assertNull($h4->getRPath());

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
    * @depends estHomeObj
    */
	public function nestHome($typ) 
	{
		$this->setTyp($typ);
		$db=$this->db;
		$db->beginTrans();

		$path1 = '/';
		$r = new Request($path1,V_S_READ);

//		$ho = new Home('/'.$this->CUser.'/1'); 
		$ho = new sessionHdl();
		
		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);	

		$h2 = $h1->getCref($this->CName,1);		
		$path2='/'.$this->CName.'/1';
		$this->assertEquals($path2,  $h2->getRPath());

		$path3 = '/'.$this->CName;
		$r = new Request($path3,V_S_CREA);
//		$ho = new Home('/'.$this->CUser.'/1'); 
		$ho = new sessionHdl();

		$h3 = new Handle($r,$ho);
		$this->assertEquals($path3,  $h3->getRPath());		

		$path4 = '/'.$this->CUser;
		$r = new Request($path4,V_S_CREA);
//		$ho = new Home('/'.$this->CUser.'/1'); 
		$ho = new sessionHdl();
		
		try {$h4 = new Handle($r,$ho);} catch (Exception $e) {$res=$e->getMessage();}
		$this->assertEquals($res,E_ERC049.':'.V_S_CREA);

		$path1 = '/';
		$r = new Request($path1,V_S_READ);
//		$ho = new Home('/');
		$ho = new sessionHdl();
		
		$h1 = new Handle($r,$ho);
		$this->assertNotNull($h1);	
		
		$act_path = $h1->getActionPath(V_S_READ);

		$this->assertNull($act_path);			

		
		$db->commit();
	}		
}
