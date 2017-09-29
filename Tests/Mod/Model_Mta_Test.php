<?php

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\UtilsC;

class Model_Mta_Test extends PHPUnit_Framework_TestCase
{
    protected static $dbs;
    protected static $prm;
    
    protected $db;
        
    public static function setUpBeforeClass()
    {
        $classes = ['Student','copyStudent'];
        $baseTypes=['dataBase','fileBase','memBase'];
        $baseName='test';
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        self::$prm=$prm;
        self::$dbs=[];
        
        Mod::reset();
        Mod::get()->init($prm['application'], $prm['handlers']);
        
        foreach ($baseTypes as $baseType) {
            self::$dbs[$baseType]=Mod::get()->getBase($baseType, $baseName);
        }
    }
    
    public function setTyp($typ)
    {
        $this->db=self::$dbs[$typ];
        $this->Cname=self::$prm[$typ]['Student'];
        $this->Cname2=self::$prm[$typ]['copyStudent'];
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
        
        $res = $mod->addAttr('today', Mtype::M_DATE);
        
        $res = $mod->addAttr('tel', Mtype::M_INT);
        
        $res = $mod->saveMod();
        
        $this->assertFalse($mod->isErr());
        
        $meta= $mod->getMeta();
        $this->assertNotNull($meta);
        
        $mod2 = new Model($this->Cname2);
        $mod2->deleteMod();
        $mod2->setMeta($meta);
        
        $res=$mod2->saveMod();
        $this->assertFalse($mod2->isErr());
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
     /**
     * @depends testSaveMod
     */
    public function testSaveMod1($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $mod = new Model($this->Cname2);
        $this->assertNotNull($mod);
        
        $this->assertTrue($mod->existsAttr('today'));
        $this->assertEquals(Mtype::M_DATE, $mod->getTyp('today'));

        $tag='attr_typ';
        $meta= $mod->getMeta();
        unset($meta[$tag]);
        $res="";
        try {
            $mod->setMeta($meta);
        } catch (Exception $e) {
            $res= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC047.':'.$mod->getModName().':'.$tag, $res);
        
        $db->commit();
    }
}
