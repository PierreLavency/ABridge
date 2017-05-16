<?php
    
require_once("Model.php");
require_once("Handler.php");

class Model_Key_Test extends PHPUnit_Framework_TestCase
{
    protected static $db1;
    protected static $db2;


    protected $Code;
    protected $CodeVal;
    protected $Student;
    protected $NoState;
    protected $db;
    
    
    public static function setUpBeforeClass()
    {
    
        resetHandlers();
        $typ='dataBase';
        $name='test';
        $Code=get_called_class().'_1';
        $CodeVal=get_called_class().'_2';
        $Student=get_called_class().'_3';
        self::$db1=getBaseHandler($typ, $name);
        initStateHandler($Code, $typ, $name);
        initStateHandler($CodeVal, $typ, $name);
        initStateHandler($Student, $typ, $name);
        
        $typ='fileBase';
        $name=$name.'_f';
        $Code=get_called_class().'_f_1';
        $CodeVal=get_called_class().'_f_2';
        $Student=get_called_class().'_f_3';
        self::$db2=getBaseHandler($typ, $name);
        initStateHandler($Code, $typ, $name);
        initStateHandler($CodeVal, $typ, $name);
        initStateHandler($Student, $typ, $name);
    }
    
    public function setTyp($typ)
    {
        if ($typ== 'SQL') {
            $this->db=self::$db1;
            $this->Code=get_called_class().'_1';
            $this->CodeVal=get_called_class().'_2';
            $this->Student=get_called_class().'_3';
        } else {
            $this->db=self::$db2;
            $this->Code=get_called_class().'_f_1';
            $this->CodeVal=get_called_class().'_f_2';
            $this->Student=get_called_class().'_f_3';
        }

        $this->NoState=get_called_class().'_f_4';
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
    
        // Ref -CodeVal
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        
        $res= $codeval->deleteMod();
        $this->assertTrue($res);
        
        $res = $codeval->addAttr('ValueName', M_STRING);
        $this->assertTrue($res);
        
        $res = $codeval->setDflt('ValueName', 'Male'); //default
        $this->assertTrue($res);
                
        $path='/'.$this->Code;
        $res = $codeval->addAttr('ValueOf', M_REF, $path);
        $this->assertTrue($res);

        $res=$codeval->setMdtr('ValueOf', true); // Mdtr
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
        
        $res = $code->addAttr('CodeName', M_STRING);
        $this->assertTrue($res);
        
        $res=$code->setBkey('CodeName', true);// unique
        $this->assertTrue($res);
        
        $path='/'.$this->CodeVal.'/ValueOf';
        $res = $code->addAttr('Values', M_CREF, $path);
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
        
        $this->assertTrue($code->isBkey('CodeName'));
        
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
        
        $this->assertTrue($codeval->isMdtr('ValueOf'));
        
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

        $this->assertEquals($code->getErrLine(), E_ERC052);
    
        
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
        
        $res=$codeval->getAllCkey();
        $l=implode(':', $res[0]);
        $this->assertEquals('ValueName:ValueOf', $l);

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
        $this->assertEquals($r->getLine($n), E_ERC031.':'.$l);

        $this->assertFalse($codeval->delAttr('ValueOf'));
        $n++;
        $this->assertEquals($r->getLine($n), E_ERC030.':ValueOf');
        
        $res=$codeval->setCkey(['ValueName','Notexist'], true);
        $this->assertFalse($res);
        $n++;
        $this->assertEquals($r->getLine($n), E_ERC002.':Notexist');
        
        $res=$codeval->setCkey('ValueName', true);
        $this->assertFalse($res);
        $n++;
        $this->assertEquals($r->getLine($n), E_ERC029);

        try {
            $res = $codeval->getBkey('Notexist', 'x');
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, E_ERC056.':Notexist');

        
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
        $this->assertEquals($log->getLine(0), E_ERC018.':CodeName:Sexe');
        
                        
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        $log = $codeval->getErrLog();
        
        $id1= $codeval->save();
        $this->assertEquals($id1, 0);
        
        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(0), E_ERC019.':ValueOf');
        
        $res = $codeval->setVal('ValueOf', null);
        $id1= $codeval->save();

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(1), E_ERC019.':ValueOf');

        $res = $codeval->isBkey('notexists');

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(2), E_ERC002.':notexists');
    
        $res = $codeval->isMdtr('notexists');

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(3), E_ERC002.':notexists');
        
        $res = $codeval->isOptl('notexists');

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(4), E_ERC002.':notexists');
        
        $res = $codeval->setMdtr('notexists', false);

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(5), E_ERC002.':notexists');
        
        $res = $codeval->setDflt('notexists', false);

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(6), E_ERC002.':notexists');
        
        $res = $codeval->getDflt('notexists');

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(7), E_ERC002.':notexists');

        $res = $codeval->setBkey('notexists', false);

        $r = $codeval-> getErrLog();
        $this->assertEquals($log->getLine(8), E_ERC002.':notexists');
        
        $this->assertFalse($codeval->isOptl('id'));
        
        $db->commit();
        
        $m = new Model('NoState');
        $r = $m-> getErrLog();
        
        $this->assertTrue($m->addAttr('A', M_STRING));
        
        $res=$m->setBkey('A', true);
        $this->assertFalse($res);
        $this->assertEquals($r->getLine(0), E_ERC017.':A');
        
        $res=$m->setCkey(['A','A'], true);
        $this->assertFalse($res);
        $this->assertEquals($r->getLine(1), E_ERC017);
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
        $this->assertTrue($code->setBkey('CodeName', false));
        $this->assertTrue($code->setBkey('CodeName', true));

        
        $this->assertTrue($code->delAttr('CodeName'));
        
        $this->assertFalse($code->existsAttr('CodeName'));
        
        $r = $code-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        $codeval = new Model($this->CodeVal);
        
        $this->assertTrue($codeval->setMdtr('ValueOf', false));
        $this->assertTrue($codeval->setMdtr('ValueOf', true));
    
        $this->assertTrue($codeval->delAttr('ValueOf'));
        
        $this->assertFalse($codeval->existsAttr('ValueOf'));
        
        $this->assertTrue($codeval->delAttr('ValueName'));
        
        $r = $codeval-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
    
        $db->commit();
    }
}
