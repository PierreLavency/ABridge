<?php
    
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\UtilsC;

class Model_Bse_Test extends PHPUnit_Framework_TestCase
{
    protected static $dbs;
    protected static $prm;
    
    protected $db;
    
    
    public static function setUpBeforeClass()
    {
        $classes = ['Student'];
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
        $this->assertNotNull($mod);
        
        $res= $mod->deleteMod();
        $this->assertTrue($res);
        
        $res = $mod->addAttr('name', Mtype::M_STRING);
        $this->assertTrue($res);

        $res = $mod->addAttr('XXX', Mtype::M_STRING);
        $this->assertTrue($res);

        $res = $mod->addAttr('tel', Mtype::M_INT);
        $this->assertTrue($res);
        
        $res = $mod->saveMod();
        $this->assertTrue($res);
    
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
    public function testSaveMod1($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $mod = new Model($this->Cname);
        $this->assertNotNull($mod);
        
        $this->assertTrue($mod->existsAttr('XXX'));
        
        $res = $mod->delAttr('XXX');
        $this->assertTrue($res);
    
        $res = $mod->addAttr('surname', Mtype::M_STRING);
        $this->assertTrue($res);
    
        $res = $mod->saveMod();
        $this->assertTrue($res);
    
        $r = $mod-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testSaveMod1
    */
    public function testNewObj($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        
        $db->beginTrans();
        
        $ins = new Model($this->Cname);
        $this->assertNotNull($ins);

        $this->assertFalse($ins->existsAttr('XXX'));
        $this->assertTrue($ins->existsAttr('surname'));
        
        $res = $ins->setVal('name', 'Lavency');
        $this->assertTrue($res);

        $res = $ins->setVal('surname', 'Pierre');
        $this->assertTrue($res);
        
        $res = $ins->setVal('tel', 123);
        $this->assertTrue($res);


        $id = $ins->save();
        $this->assertEquals($id, 1);

        $r = $ins-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testNewObj
    */
    public function testUpdateObj($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        
        $db->beginTrans();

        $ins=new Model($this->Cname, 1);
        $this->assertNotNull($ins);
    
        $res  = $ins->getVal('name');
        $this->assertEquals($res, 'Lavency');
        
        $res  = $ins->getVal('surname');
        $this->assertEquals($res, 'Pierre');
        
        $res  = $ins->getVal('tel');
        $this->assertEquals($res, 123);

        $res  = $ins->setVal('surname', 'Renaud');
        $this->assertTrue($res);

        $id = $ins->save();
        $this->assertEquals($id, 1);

        $r = $ins-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testUpdateObj
    */
    public function testDeleteObj($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        
        $db->beginTrans();

        $ins=new Model($this->Cname, 1);
        $this->assertNotNull($ins);
    
        $res  = $ins->getVal('name');
        $this->assertEquals($res, 'Lavency');
        
        $res  = $ins->getVal('surname');
        $this->assertEquals($res, 'Renaud');
        
        $res  = $ins->getVal('tel');
        $this->assertEquals($res, 123);
        
        $id = $ins->delet();
        $this->assertTrue($id);

        $r = $ins-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testDeleteObj
    */
    public function testDeleteMod($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        
        $db->beginTrans();

        $res = false;
        try {
            $x= new Model($this->Cname, 1);
        } catch (Exception $e) {
            $res=true;
        }

        $this->assertTrue($res);
        
        $ins=new Model($this->Cname);
        $this->assertNotNull($ins);
    
        $res  = $ins->existsAttr('surname');
        $this->assertTrue($res);
        
        $res  = $ins->deleteMod();
        $this->assertTrue($res);
        
        $res  = $ins->existsAttr('surname');
        $this->assertFalse($res);

        $r = $ins-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();
    }

    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testDeleteMod
    */
    public function testError($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $mod = new Model($this->Cname);
        $this->assertNotNull($mod);
        
        $res = $mod->addAttr('name', Mtype::M_STRING);
        $this->assertTrue($res);
        
        $res = $mod->saveMod();
        $this->assertTrue($res);
        
        $res = $mod->setVal('name', 'Lavency');
        $this->assertTrue($res);

        $res = $mod->addAttr('XXX', Mtype::M_STRING);
        $this->assertTrue($res);

        $id = $mod->save();
        $this->assertFalse($id);
    
        $r = $mod-> getErrLog();
        $this->assertEquals($r->getLine(0), CstError::E_ERC024);
        
        $db->commit();
    }
}
