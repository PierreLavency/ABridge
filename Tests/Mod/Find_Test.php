<?php

use ABridge\ABridge\Mod\Find;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class Find_Test extends PHPUnit_Framework_TestCase
{
    protected $Cname = 'students';
    protected $db;
    
    protected static $dbs;
    protected static $prm;
    
    public static function setUpBeforeClass()
    {
    
        $classes = ['Student'];
        $baseTypes=['dataBase','fileBase','memBase'];
        $baseName='test';
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        
        self::$prm=$prm;
        self::$dbs=[];
        
        Mod::reset();
        $mod=Mod::get();
        $mod->init($prm['application'], $prm['handlers']);
        
        foreach ($baseTypes as $baseType) {
            self::$dbs[$baseType]=Mod::get()->getBase($baseType, $baseName);
        }
    }
    
    public function setTyp($typ)
    {
        $this->db=self::$dbs[$typ];
        $this->Cname=self::$prm[$typ]['Student'];
    }
    
    public function Provider1()
    {
        return [['dataBase'],['fileBase'],['memBase']];
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
        
        $res = $mod->addAttr('name', Mtype::M_STRING);
        $res = $mod->setProp('name', Model::P_BKY);
        
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
