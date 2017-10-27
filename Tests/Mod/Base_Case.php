<?php
    
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Mod\SQLBase;
use ABridge\ABridge\CstError;

class Base_Case extends PHPUnit_Framework_TestCase
{

    protected $test1 =['CODE'=> '001', 'SEVERITY'=> 1, 'vnum'=>1];
    protected $test2 =['CODE'=> '002', 'SEVERITY'=> 2,'vnum'=>1];
    protected $test3 =['CODE'=> '001', 'CODE'=> 0,'vnum'=>1];
    protected $meta=[
                        'attr_inhnme' => false,
                        'attr_typ'=> ["vnum"=>Mtype::M_INT,"ctstp"=>Mtype::M_TMSTP,"utstp"=>Mtype::M_TMSTP,'CODE'=>Mtype::M_STRING,'SEVERITY'=>Mtype::M_INT,],
                    ];
    
    protected $id1=1;
    protected $id2=2;
    
    protected static $CName;
    protected static $CName2;
    protected static $DBName;
    protected static $db;
    protected static $baseType;

    public function testNewMod()
    {
        $db=self::$db;
        $this->assertEquals(self::$baseType, $db->getBaseType());
        $db->beginTrans();
        $this->assertTrue($db->newModId(self::$CName, $this->meta, true, $this->meta));
        $this->assertTrue($db->newModId(self::$CName2, [], true, []));
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
        $this->assertTrue(in_array(self::$CName, $modL));
        $this->assertEquals($this->meta, $db->getMod(self::$CName));
        $this->assertEquals([], $db->getMod(self::$CName2));
        $this->assertTrue($db->putMod(self::$CName, [], [], $this->meta));
        $this->assertTrue($db->putMod(self::$CName2, $this->meta, $this->meta, []));
        $db->commit();
    }

    /**
    * @depends testPutMod
    */
    public function testDelMod()
    {
        $db=self::$db;
        $db->beginTrans();
        $this->assertEquals([], $db->getMod(self::$CName));
        $this->assertEquals($this->meta, $db->getMod(self::$CName2));
        $this->assertTrue($db->delMod(self::$CName2, $this->meta));
        $db->commit();
    }
    /**
    * @depends testDelMod
    */
    
    public function testNewObj()
    {
        $db=self::$db;
        $db->beginTrans();
        $this->assertTrue($db->putMod(self::$CName, $this->meta, $this->meta, []));
        $this->assertFalse($db->existsMod(self::$CName2));
        $this->assertTrue($db->newModId(self::$CName2, [], false, []));
        $this->assertTrue($db->putMod(self::$CName2, $this->meta, $this->meta, []));
        $this->assertEquals($this->id1, self::$db->newObj(self::$CName, $this->test1));
        $this->assertEquals($this->id2, self::$db->newObj(self::$CName, $this->test2));
        $this->assertEquals($this->id1, self::$db->newObjId(self::$CName2, $this->test1, $this->id1));
        $this->assertEquals($this->id2, self::$db->newObjId(self::$CName2, $this->test2, $this->id2));

        $r=false;
        try {
            $db->newObj(self::$CName2, $this->test1);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $db->newObjId(self::$CName2, $this->test1, $this->id2);
        } catch (Exception $e) {
            $r = true;
        }
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
        $this->assertEquals($this->test1, $x->getObj(self::$CName, $this->id1));
        $this->assertEquals($this->test2, $x->getObj(self::$CName, $this->id2));
        $this->assertEquals($this->id1, $x->putObj(self::$CName, $this->id1, 1, $this->test2));
        $this->assertEquals($this->id2, $x->putObj(self::$CName, $this->id2, 1, $this->test1));
        $x->commit();
    }
    /**
    * @depends testPutObj
    */

    public function testDelObj()
    {
        $x = self::$db;
        $x->beginTrans();
        $this->assertEquals($this->test2, $x->getObj(self::$CName, $this->id1));
        $this->assertEquals($this->test1, $x->getObj(self::$CName, $this->id2));
        $this->assertTrue($x->delObj(self::$CName, $this->id2));
        $x->commit();
    }
    /**
    * @depends testDelObj
    */

    public function testRollBack()
    {
        $x = self::$db;

        $x->beginTrans();
        $this->assertEquals(0, $x->getObj(self::$CName, $this->id2));
        $this->assertTrue($x->delObj(self::$CName, $this->id1));
        $r = $x->rollback();
        $x->beginTrans();
        $this->assertEquals($this->test2, $x->getObj(self::$CName, $this->id1));
    }

    /**
    * @depends  testRollBack
    */
    
    public function testFindObj()
    {
        $x = self::$db;
        $x->beginTrans();
        $n=10;
        for ($i=0; $i<$n; $i++) {
            $code = '0'.$i;
            $j=$i;
            if ($i < ($n/2)) {
                $j=1;
            } else {
                $j=-$i;
            }
            $test= ['CODE'=>$code,'SEVERITY'=>$j];
            $id = $x->newObj(self::$CName, $test);
        }
        $n2= $n/2;
        $this->assertEquals($id, ($n+2));
        $this->assertEquals(($n/2), count($x->findObj(self::$CName, 'SEVERITY', 1)));
        $this->assertEquals(1, count($x->findObj(self::$CName, 'CODE', '01')));
        $this->assertEquals(1, count($x->findObj(self::$CName, 'id', 1)));
        
        $this->assertEquals($n+1, count($x->findObjWheOp(self::$CName, [], [], [], [])));
        $this->assertEquals(1, count($x->findObjWheOp(self::$CName, ['CODE','SEVERITY'], [], ['01',1], [])));
        
        $this->assertEquals(1, count($x->findObjWheOp(self::$CName, ['CODE','SEVERITY'], [], ['01',1], [])));
        $this->assertEquals($n2+1, count($x->findObjWheOp(self::$CName, ['SEVERITY'], ['SEVERITY'=>'>'], [0], [])));
        $this->assertEquals($n2, count($x->findObjWheOp(self::$CName, ['SEVERITY'], ['SEVERITY'=>'<'], [0], [])));
        $this->assertEquals($n2, count($x->findObjWheOp(self::$CName, ['SEVERITY'], ['SEVERITY'=>'<'], [1], [])));
        $this->assertEquals(1, count($x->findObjWheOp(self::$CName, ['id'], ['id'=>'='], [1], [])));
        $this->assertEquals(1, count($x->findObjWheOp(self::$CName, ['id'], ['id'=>'<'], [2], [])));
        $this->assertEquals(0, count($x->findObjWheOp(self::$CName, ['id'], ['id'=>'>'], [1000], [])));
        $this->assertEquals($n+1, count($x->findObjWheOp(self::$CName, ['CODE'], ['CODE'=>'::'], ['0'], [])));
        $this->assertEquals(0, count($x->findObjWheOp(self::$CName, ['CODE'], ['CODE'=>'::'], ['x'], [])));
        
        $res = $x->findObjWheOp(self::$CName, ['SEVERITY'], ['SEVERITY'=>'<'], [0], [['SEVERITY',false]]);
        $this->assertEquals($n2, count($res));
        $this->assertEquals($id, $res[0]);
        
        $x->commit();
    }
    /**
    * @depends  testFindObj
    */
    
    public function testErr()
    {
        $x = self::$db;
        $x->beginTrans();
        
        $this->assertFalse($x->newModId(self::$CName, $this->meta, true, $this->meta));
        $this->assertFalse($x->getObj(self::$CName, 0));
        $this->assertTrue($x->delObj(self::$CName, 0));
        $this->assertTrue($x->delObj(self::$CName, 10000));
        $this->assertFalse($x->getObj(self::$CName, 10000));
        $this->assertFalse($x->putObj(self::$CName, 0, 1, $this->test2));
        $this->assertFalse($x->putObj(self::$CName, 10000, 1, $this->test2));
        $this->assertFalse($x->putMod('NOTEXISTS', [], [], $this->meta));
        $this->assertFalse($x->getObj('NOTEXISTS', $this->id2));
        $this->assertFalse($x->newObj('NOTEXISTS', $this->id2));
        $this->assertFalse($x->delObj('NOTEXISTS', $this->id2));
        $this->assertFalse($x->putObj('NOTEXISTS', $this->id1, 1, $this->test2));
        $this->assertFalse($x->findObj('NOTEXISTS', 'CODE', '01'));
        $r='';
        try {
            $x->findObjWheOp('NOTEXISTS', ['CODE'], [], ['01'], []);
        } catch (Exception $e) {
            $r = $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC022.':NOTEXISTS', $r);
        $r='';
        try {
            $x->findObjWheOp('NOTEXISTS', ['CODE'], ['='], ['01'], []);
        } catch (Exception $e) {
            $r = $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC022.':NOTEXISTS', $r);

        $x->commit();
    }


    
    /**
    * @depends  testErr
    */
    public function testPutMod2()
    {
        $x = self::$db;
        $x->beginTrans();
        
        $this->assertTrue($x->putMod(self::$CName, [], [], $this->meta));
        $this->assertEquals([], $x->getMod(self::$CName));
        
        
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
        try {
            $x->close();
        } catch (Exception $e) {
            $r =true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->beginTrans();
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->commit();
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->rollback();
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        
// no error if no begin transaction 
        
// all fails since closed 
        $r=false;
        try {
            $x->getAllMod();
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->existsMod('notexists');
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->newModId('notexists', [], true, []);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->getMod('notexists');
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->putMod('notexists', [], [], []);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->delMod('notexists');
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        try {
            $x->newObj(self::$CName, []);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->getObj(self::$CName, 1);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->putObj(self::$CName, 1, 0, []);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->delObj(self::$CName, 1);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        $r=false;
        try {
            $x->findObj(self::$CName, 'id', 1);
        } catch (Exception $e) {
            $r = true;
        }
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
            
            $err=['attr_lst'=>["NULL"],'attr_plst'=>['NULL'],'attr_typ'=> ["NULL"=>Mtype::M_INT]];
            
            $r=false;
            $x->delMod('test');
            try {
                $x->newModId('test', $err, true, $err);
            } catch (Exception $e) {
                $r = true;
            }
            $this->assertTrue($r);
            
            $r=false;
            try {
                $x->putMod(self::$CName, $this->meta, $err, []);
            } catch (Exception $e) {
                $r = true;
            }
            $this->assertTrue($r);

            $err =['CODE'=> null, 'notexists'=> 2];

            $r=false;
            try {
                $x->newObj(self::$CName, $err);
            } catch (Exception $e) {
                $r = true;
            }
            $this->assertTrue($r);
            
            $r=false;
            try {
                $x->putObj(self::$CName, 1, $err);
            } catch (Exception $e) {
                $r = true;
            }
            $this->assertTrue($r);

            $r=false;
            try {
                $x->delObj(self::$CName, 'err');
            } catch (Exception $e) {
                $r = true;
            }
            $this->assertTrue($r);
        }
    }
}
