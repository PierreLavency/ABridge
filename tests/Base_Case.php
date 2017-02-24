<?php
    
/* */

require_once("SQLBase.php"); 

class Base_Case extends PHPUnit_Framework_TestCase {

    protected $test1 =['CODE'=> '001', 'SEVERITY'=> 1];
    protected $test2 =['CODE'=> '002', 'SEVERITY'=> 2];
    protected $test3 =['CODE'=> '001', 'CODE'=> 0];
    protected $meta=[
                        'attr_lst'=>['vnum','ctstp','utstp','CODE','SEVERITY',],
                        'attr_plst'=>['vnum','ctstp','utstp','CODE','SEVERITY',],
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
		$modL=$db->getAllMod();
		$this->assertTrue(in_array(self::$CName,$modL));
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
		$this->assertTrue($db->newModId(self::$CName2,[],false));
	    $this->assertTrue($db->putMod(self::$CName2,$this->meta,$this->meta,[]));   	
        $this->assertEquals($this->id1,self::$db->newObj(self::$CName,$this->test1));       
        $this->assertEquals($this->id2,self::$db->newObj(self::$CName,$this->test2));
        $this->assertEquals($this->id1,self::$db->newObjId(self::$CName2,$this->test1,$this->id1));       
        $this->assertEquals($this->id2,self::$db->newObjId(self::$CName2,$this->test2,$this->id2));	

		$r=false;
        try {$db->newObj(self::$CName2, $this->test1);} catch (Exception $e) {$r = true;}
		$this->assertTrue($r);
		$r=false;
        try {$db->newObjId(self::$CName2, $this->test1,$this->id2);} catch (Exception $e) {$r = true;}
		$this->assertTrue($r);
		
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
		$this->assertEquals(1,count($x->findObj(self::$CName,'id',1)));
		
		$this->assertEquals($n+1,count($x->findObjWheOp(self::$CName,[],[],[])));
		$this->assertEquals(1,count($x->findObjWheOp(self::$CName,['CODE','SEVERITY'],[],['01',1])));
		
		$this->assertEquals(1,count($x->findObjWheOp(self::$CName,['CODE','SEVERITY'],[],['01',1])));
		$this->assertEquals($n+1,count($x->findObjWheOp(self::$CName,['SEVERITY'],['SEVERITY'=>'>'],[0])));	
		$this->assertEquals(0,count($x->findObjWheOp(self::$CName,['SEVERITY'],['SEVERITY'=>'<'],[0])));	
		$this->assertEquals(0,count($x->findObjWheOp(self::$CName,['SEVERITY'],['SEVERITY'=>'<'],[1])));	
		$this->assertEquals(1,count($x->findObjWheOp(self::$CName,['id'],['id'=>'='],[1])));	
		$this->assertEquals(1,count($x->findObjWheOp(self::$CName,['id'],['id'=>'<'],[2])));	
		$this->assertEquals(0,count($x->findObjWheOp(self::$CName,['id'],['id'=>'>'],[1000])));		
		$this->assertEquals($n+1,count($x->findObjWheOp(self::$CName,['CODE'],['CODE'=>'::'],['0'])));		
		$this->assertEquals(0,count($x->findObjWheOp(self::$CName,['CODE'],['CODE'=>'::'],['x'])));	
		
        $x->commit();
        
    }
    /**
    * @depends  testFindObj
    */
    
    public function testErr() 
    {
        $x = self::$db; 
        $x->beginTrans();
        
        $this->assertFalse($x->newMod(self::$CName,$this->meta));
        $this->assertFalse($x->getObj(self::$CName,0));
        $this->assertTrue($x->delObj(self::$CName,0));
        $this->assertTrue($x->delObj(self::$CName,10000));
        $this->assertFalse($x->getObj(self::$CName,10000));
        $this->assertFalse($x->putObj(self::$CName,0,$this->test2));
        $this->assertFalse($x->putObj(self::$CName,10000,$this->test2));
        $this->assertFalse($x->putMod('NOTEXISTS',[],[],$this->meta));
        $this->assertFalse($x->getObj('NOTEXISTS',$this->id2));
        $this->assertFalse($x->newObj('NOTEXISTS',$this->id2));
        $this->assertFalse($x->delObj('NOTEXISTS',$this->id2));
        $this->assertFalse($x->putObj('NOTEXISTS',$this->id1,$this->test2));
        $this->assertFalse($x->findObj('NOTEXISTS','CODE','01'));
        $this->assertFalse($x->findObjWheOp('NOTEXISTS',['CODE'],[],['01']));
	    $this->assertFalse($x->findObjWheOp('NOTEXISTS',['CODE'],['='],['01']));


		
        $x->commit();
    }

    /**
    * @depends  testErr
    */
    public function testLog() 
    {
        $x = self::$db; 
        $x->beginTrans();
        
        $this->assertFalse($x->getLog());
        $this->assertTrue($x->setLogLevl (1));
        $this->assertEquals(1,count($x->findObj(self::$CName,'CODE','01')));
        $this->assertNotNull($l=$x->getLog());
        $this->assertEquals(1,$l->logSize());
        $x->commit();
    }   
    
    /**
    * @depends  testLog
    */
    public function testPutMod2() 
    {
        $x = self::$db; 
        $x->beginTrans();
        
        $this->assertTrue($x->putMod(self::$CName,[],[],$this->meta));
        $this->assertEquals([],$x->getMod(self::$CName));
        
        
        $x->commit();
    }
    
    /**
    * @depends  testPutMod2
    */
    public function testclose() 
    {
        $x = self::$db; 
        $x->beginTrans();
        
        $this->assertTrue($x->close());

        $r=false;   
        try {$x->close();} catch (Exception $e) {$r =true;}
        $this->assertTrue($r);
        $r=false;   
        try {$x->beginTrans();} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);      
        $r=false;
        try {$x->commit();} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);
        $r=false;
        try {$x->rollback();} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);
// no error if no begin transaction 
        
// all fails since closed 
        $r=false;
        try {$x->getAllMod ();} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);       
        $r=false;
        try {$x->existsMod ('notexists');} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);      
        $r=false;
        try {$x->newMod('notexists',[]);} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);  
        $r=false;
        try {$x->getMod('notexists');} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);
        $r=false;
        try {$x->putMod('notexists',[],[],[]);} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);
        $r=false;
        try {$x->delMod('notexists');} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);
        try {$x->newObj(self::$CName, []);} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);      
        $r=false;
        try {$x->getObj(self::$CName, 1);} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);  
        $r=false;
        try {$x->putObj(self::$CName, 1 , []);} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);  
        $r=false;
        try {$x->delObj(self::$CName, 1);} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);      
        $r=false;
        try {$x->findObj(self::$CName,'id', 1);} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);
 

    }
    
    /**
    * @depends  testclose
    */
    public function testSqlErr() 
    {
        $x = self::$db; 

        if (get_class($x)=='SQLBase') {
        
            $this->assertTrue($x->connect());
            
            $err=['attr_lst'=>["NULL"],'attr_plst'=>['NULL'],'attr_typ'=> ["NULL"=>M_INT]];
            
            $r=false;
            $x->delMod('test');
            try {$x->newMod('test', $err);} catch (Exception $e) {$r = true;}
            $this->assertTrue($r);      
            
            $r=false;
            try {$x->putMod(self::$CName,$this->meta,$err,[]);} catch (Exception $e) {$r = true;}
            $this->assertTrue($r);      

            $err =['CODE'=> NULL, 'notexists'=> 2];

            $r=false;
            try {$x->newObj(self::$CName,$err);} catch (Exception $e) {$r = true;}
            $this->assertTrue($r);      
            
            $r=false;
            try {$x->putObj(self::$CName,1,$err);} catch (Exception $e) {$r = true;}
            $this->assertTrue($r);      

            $r=false;
            try {$x->delObj(self::$CName,'err');} catch (Exception $e) {$r = true;}
            $this->assertTrue($r);

            $r=false;
            try {new SQLBase(self::$DBName,'Notexist','Notexist');} catch (Exception $e) {$r = true;}
            $this->assertTrue($r);
        }

    }

}