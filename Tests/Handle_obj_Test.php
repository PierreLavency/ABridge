<?php

require_once 'Handler.php'; 
require_once 'Handle.php'; 
require_once 'CstMode.php';
require_once 'CstType.php';

class Handle_obj_Test extends PHPUnit_Framework_TestCase  
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
        initStateHandler ($CName    ,$typ, $name);
        initStateHandler ($CUser    ,$typ, $name);
        initStateHandler ($CCode    ,$typ, $name); 
 
        $typ='fileBase';
        $name=$name.'_f';
        $CName='Examplef';
        $CUser='Userf';
		$CCode='Codef';
        self::$db2=getBaseHandler ($typ, $name);
        initStateHandler ($CName    ,$typ, $name);
        initStateHandler ($CUser    ,$typ, $name);
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
        $res=$mod->setVal('Ref',$id2);  
        $id4 = $mod->save();    
        $r = $mod-> getErrLog ();
        $this->assertEquals($r->logSize(),0);       

        $id=2;      
        $path1 = '/'.$this->CName.'/'.$id;
        $r = new Request($path1,V_S_READ);
        $ho = new Home('/'); 
        
        $h = new Handle($r,$ho);
        $this->assertFalse($h->nullObj());
        $this->assertEquals($this->CName,$h->getModName());
		$this->assertEquals($this->CName,$h->getModCref('CRef'));
        $this->assertEquals($id,$h->getId());
        $aList = $h->getAttrList();
        $this->assertEquals(8, count($aList));
        $this->assertEquals(M_REF, $h->getTyp('Ref'));
        $this->assertEquals(1, $h->getDflt($this->CUser));  
        $this->assertEquals($id, $h->getVal('id'));
        $this->assertEquals(2, count($h->getValues($this->CUser)));
        $this->assertFalse($h->isMdtr($this->CUser));
        $this->assertFalse($h->isEval($this->CUser));
        $this->assertTrue($h->isModif($this->CUser));
        $this->assertTrue($h->isSelect($this->CUser));
        $this->assertEquals($id,$h->save());
        $this->assertFalse($h->isErr());
        $this->assertEquals(0,$h->getErrLog()->logSize());
        $this->assertTrue($h->setCriteria(['Ref'], ['='], [1]));
        $this->assertEquals(1,count($h->select()));

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
        $id=4;      
        $path1 = '/'.$this->CName.'/'.$id;
        $r = new Request($path1,V_S_READ);
        $ho = new Home('/'); 
        
        $h = new Handle($r,$ho);
        $this->assertTrue($h->delet()); 
        
        $db->commit();
    }       
}
