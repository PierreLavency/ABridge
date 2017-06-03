<?php

require_once 'Find.php';
require_once("Model.php");
require_once("Handler.php");

class Find_Test extends PHPUnit_Framework_TestCase
{
    protected static $db1;
    protected static $db2;

    protected $Cname = 'students';
    protected $db;
    
    
    public static function setUpBeforeClass()
    {
    
        resetHandlers();
        $typ='dataBase';
        $name='test';
        $Cname=get_called_class().'_1';
        self::$db1=getBaseHandler($typ, $name);
        initStateHandler($Cname, $typ, $name);
        
        $typ='fileBase';
        $name=$name.'_f';
        $Cname=get_called_class().'_f_1';
        self::$db2=getBaseHandler($typ, $name);
        initStateHandler($Cname, $typ, $name);
    }
    
    public function setTyp($typ)
    {
        if ($typ== 'SQL') {
                $this->db=self::$db1;
                $this->Cname=get_called_class().'_1';
        } else {
            $this->db=self::$db2;
            $this->Cname=get_called_class().'_f_1';
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
        
        $mod = new Model($this->Cname);
        
        $res= $mod->deleteMod();
        
        $res = $mod->addAttr('name', M_STRING);
        $res = $mod->setBkey('name', true);
        
        $res = $mod->saveMod();
    
        $r = $mod-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testSaveMod
    */
    public function testNOobj($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $this->assertFalse(Find::existsObj($this->Cname));
        $this->assertEquals([], Find::allId($this->Cname));
        $this->assertNull(Find::byKey($this->Cname, 'name', 'x'));
        
        $x = new Model($this->Cname);
        $x->setVal('name', 'x');
        $x->save();
        $r = $x-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testNOobj
    */
    public function testObj($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        
        $db->beginTrans();
        
        $this->assertTrue(Find::existsObj($this->Cname));
        $this->assertEquals([1], Find::allId($this->Cname));
        $this->assertNotNull(Find::byKey($this->Cname, 'name', 'x'));

        $db->commit();
    }
}
