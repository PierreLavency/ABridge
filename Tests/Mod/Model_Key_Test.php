<?php
    
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\UtilsC;

class Model_Key_Test extends PHPUnit_Framework_TestCase
{
    protected static $db1;
    protected static $db2;
    
    protected static $dbs;
    protected static $prm;

    protected $Code;
    protected $CodeVal;
    protected $Student;
    protected $NoState;
    protected $db;
    
    
    public static function setUpBeforeClass()
    {
        $classes = ['Code','Student','CodeVal'];
        $baseTypes=['dataBase','fileBase','memBase'];
        $baseName='test';
        
        $prm=UtilsC::genPrm($classes, get_called_class(), $baseTypes);
        
        self::$prm=$prm;
        self::$dbs=[];
        
        Mod::reset();
        Mod::get()->init($prm['application'], $prm['handlers']);
        
        foreach ($baseTypes as $baseType) {
            self::$dbs[$baseType]=Mod::get()->getBase($baseType, $baseName);
        };
    }
    
    public function setTyp($typ)
    {
 
        $this->db=self::$dbs[$typ];
        $this->Code=self::$prm[$typ]['Code'];
        $this->CodeVal=self::$prm[$typ]['CodeVal'];
        $this->Student=self::$prm[$typ]['Student'];
        
        $this->NoState=get_called_class().'_f_4';
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
    
        // Ref -CodeVal
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        
        $res= $codeval->deleteMod();
        $this->assertTrue($res);
        
        $res = $codeval->addAttr('ValueName', Mtype::M_STRING);
        $this->assertTrue($res);
        
        $res = $codeval->setDflt('ValueName', 'Male'); //default
        $this->assertTrue($res);
                
        $path='/'.$this->Code;
        $res = $codeval->addAttr('ValueOf', Mtype::M_REF, $path);
        $this->assertTrue($res);

        $res=$codeval->setProp('ValueOf', Model::P_MDT);
        $this->assertTrue($res);

        $res = $codeval->saveMod();
        $this->assertTrue($res);
        
        $r = $codeval-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        // CRef - Code
        $code = new Model($this->Code);
        $this->assertNotNull($code);
    
        $res= $code->deleteMod();
        $this->assertTrue($res);
        
        $res = $code->addAttr('CodeName', Mtype::M_STRING);
        $this->assertTrue($res);
        
        $res=$code->setProp('CodeName', Model::P_BKY);// unique
        $this->assertTrue($res);
        
        $path='/'.$this->CodeVal.'/ValueOf';
        $res = $code->addAttr('Values', Mtype::M_CREF, $path);
        $this->assertTrue($res);
        
        $res = $code->saveMod();
        $this->assertTrue($res);

        $r = $code-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        $db->commit();
    }

    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testSaveMod
    */
    public function testNewCode($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();

        // Sexe
        
        $code = new Model($this->Code);
        $this->assertNotNull($code);
        
        $this->assertTrue($code->isProp('CodeName', Model::P_BKY));
        
        $this->assertNull($code->getDflt('CodeName'));
        
        $this->assertTrue($code->setVal('CodeName', null));

        $bk = $code->getBkey('CodeName', 'Sexe');
        $this->assertNull($bk);

        $res = $code->setVal('CodeName', 'Sexe');
        $this->assertTrue($res);

        $this->assertFalse($code->isOptl('Values'));
        
        $id = $code->save();
        $this->assertEquals($id, 1);
                
        $res = $code->setVal('CodeName', 'Sexe');
        $this->assertTrue($res);

        $id = $code->save();
        $this->assertEquals($id, 1);
//
        $res = $code->setVal('CodeName', null);
        $this->assertTrue($res);

        $id = $code->save();
        $this->assertEquals($id, 1);

        $res = $code->setVal('CodeName', 'Sexe');
        $this->assertTrue($res);

        $id = $code->save();
        $this->assertEquals($id, 1);
//
        
        $r = $code-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();

        // check reload
        
        $code = new Model($this->Code, $id);
        $this->assertNotNull($code);
        
        $r = $code-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

// 		getBkey

        $bk = $code->getBkey('CodeName', 'Sexe');
        $this->assertEquals($id, $bk->getId());
        
        //  Male
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        
        // check defaut and null
        $res=$codeval->getVal('ValueName');
        $this->assertNull($res);

        $res=$codeval->getDflt('ValueName');
        $this->assertEquals($res, 'Male');

        // check mandatory
        
        $res = $codeval->setVal('ValueName', $res);
        $this->assertTrue($res);
        
        $this->assertTrue($codeval->isOptl('ValueName'));
        
        $this->assertTrue($codeval->isProp('ValueOf', Model::P_MDT));
        
        $this->assertFalse($codeval->isOptl('ValueOf'));
        
        $res = $codeval->setVal('ValueOf', $id);
        $this->assertTrue($res);
        
        $id1= $codeval->save();
        $this->assertEquals($id1, 1);

        $r = $codeval-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        // check deletable
        
        $this->assertFalse($code->isDel());

        $code->delet();

        $this->assertEquals($code->getErrLine(), CstError::E_ERC052);
    
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testNewCode
    */
    public function testCkey($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $codeval = new Model($this->CodeVal);

        $res=$codeval->setCkey(['ValueName','ValueOf'], true);
        $this->assertTrue($res);
/*        
        $res=$codeval->getAllCkey();
        $l=implode(':', $res[0]);
        $this->assertEquals('ValueName:ValueOf', $l);
*/
        $res=$codeval->saveMod();
        $this->assertTrue($res);
        
        $res = $codeval->setVal('ValueName', 'Female');
        $res = $codeval->setVal('ValueOf', 1);

        $id2= $codeval->save();
        $this->assertEquals($id2, 2);
        
        $r = $codeval-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        $db->commit();

        $codeval = new Model($this->CodeVal, 2);
        $r = $codeval-> getErrLog();
        
        $id2= $codeval->save();
        $r->show();
        $this->assertEquals($id2, 2);
        
        $r = $codeval-> getErrLog();

        $this->assertEquals($r->logSize(), 0);
        $db->commit();
        
        $codeval = new Model($this->CodeVal);
        
        $res = $codeval->setVal('ValueName', null);
        $res = $codeval->setVal('ValueOf', 1);

        $id3= $codeval->save();
        $this->assertEquals($id3, 3);
        
        //errors
        
        $codeval = new Model($this->CodeVal);
        
        $res = $codeval->setVal('ValueName', 'Female');
        $res = $codeval->setVal('ValueOf', 1);

        $id2= $codeval->save();
        $this->assertFalse($id2);
        
        $n = 0;
        $r = $codeval-> getErrLog();
        $this->assertEquals($r->getLine($n), CstError::E_ERC031.':'.'ValueName:ValueOf');

        $this->assertFalse($codeval->delAttr('ValueOf'));
        $n++;
        $this->assertEquals($r->getLine($n), CstError::E_ERC030.':ValueOf');
        
        $res=$codeval->setCkey(['ValueName','Notexist'], true);
        $this->assertFalse($res);
        $n++;
        $this->assertEquals($r->getLine($n), CstError::E_ERC002.':Notexist');
        
        $res=$codeval->setCkey('ValueName', true);
        $this->assertFalse($res);
        $n++;
        $this->assertEquals($r->getLine($n), CstError::E_ERC029);

        try {
            $res = $codeval->getBkey('Notexist', 'x');
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC056.':Notexist');

        
        $db->commit();
        
        $codeval = new Model($this->CodeVal);

        $res=$codeval->setCkey(['ValueName','ValueOf'], false);
        $this->assertTrue($res);
        
        $res=$codeval->saveMod();
        $this->assertTrue($res);
    }
    
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testCkey
    */
    public function testErrors($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();

        $code = new Model($this->Code);
        $this->assertNotNull($code);
        $log = $code->getErrLog();

        
        $res = $code->setVal('CodeName', 'Sexe');
        $this->asserttrue($res);
        $code->save();
        $this->assertEquals($log->getLine(0), CstError::E_ERC018.':CodeName:Sexe');
        
                        
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        $log = $codeval->getErrLog();
        
        $id1= $codeval->save();
        $this->assertEquals($id1, 0);
        
        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC019.':ValueOf');
        
        $res = $codeval->setVal('ValueOf', null);
        $id1= $codeval->save();

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC019.':ValueOf');
/*
        $res = $codeval->isProp('notexists',Model::P_BKY);

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC002.':notexists');

        $res = $codeval->isProp('notexists', Model::P_MDT);

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC002.':notexists');
*/
        $res = $codeval->isOptl('notexists');

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC002.':notexists');
        /*
        $res = $codeval->setMdtr('notexists', false);

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC002.':notexists');
        */
        $res = $codeval->setDflt('notexists', false);

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC002.':notexists');
        
        $res = $codeval->getDflt('notexists');

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC002.':notexists');

        $res = $codeval->unsetProp('notexists', Model::P_BKY);

        $r = $codeval-> getErrLog();
        $this->assertEquals($codeval->getErrLine(), CstError::E_ERC002.':notexists');
        
        $this->assertFalse($codeval->isOptl('id'));
        
        $db->commit();
        
        $m = new Model('NoState');
        $r = $m-> getErrLog();
        
        $this->assertTrue($m->addAttr('A', Mtype::M_STRING));
/*        
        $res=$m->setProp('A', Model::P_BKY);
        $this->assertFalse($res);
        $this->assertEquals($r->getLine(0), CstError::E_ERC017.':A');
 */
        $res=$m->setCkey(['A','A'], true);
        $this->assertFalse($res);
        $this->assertEquals($m->getErrLine(), CstError::E_ERC017);
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testErrors
    */
    public function testDelAttr($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        
        $code = new Model($this->Code);
        $this->assertTrue($code->unsetProp('CodeName', Model::P_BKY));
        $this->assertTrue($code->setProp('CodeName', Model::P_BKY));

        
        $this->assertTrue($code->delAttr('CodeName'));
        
        $this->assertFalse($code->existsAttr('CodeName'));
        
        $r = $code-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        $codeval = new Model($this->CodeVal);
        
        $this->assertTrue($codeval->unsetProp('ValueOf', Model::P_MDT));
        $this->assertTrue($codeval->setProp('ValueOf', Model::P_MDT));
    
        $this->assertTrue($codeval->delAttr('ValueOf'));
        
        $this->assertFalse($codeval->existsAttr('ValueOf'));
        
        $this->assertTrue($codeval->delAttr('ValueName'));
        
        $r = $codeval-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
    
        $db->commit();
    }
}
