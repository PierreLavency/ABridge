<?php
    
use ABridge\ABridge\Model;
use ABridge\ABridge\Handler;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mtype;

class Model_Bse_Test extends PHPUnit_Framework_TestCase
{
    protected static $db1;
    protected static $db2;

    protected $Cname = 'students';
    protected $db;
    
    
    public static function setUpBeforeClass()
    {
    
        Handler::get()->resetHandlers();
        $typ='dataBase';
        $name='test';
        $Cname=get_called_class().'_1';
        self::$db1=Handler::get()->getBase($typ, $name);
        Handler::get()->setStateHandler($Cname, $typ, $name);
        
        $typ='fileBase';
        $name=$name.'_f';
        $Cname=get_called_class().'_f_1';
        self::$db2=Handler::get()->getBase($typ, $name);
        Handler::get()->setStateHandler($Cname, $typ, $name);
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

        $hdl = Handler::get()->getStateHandler($this->Cname);
        $res=$hdl->findObj($this->Cname, 'id', 1);
        $this->assertEquals($res, []);
        
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
