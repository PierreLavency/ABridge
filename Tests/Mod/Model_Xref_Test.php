<?php
    
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Handler;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

class Model_Xref_Test extends PHPUnit_Framework_TestCase
{
    protected static $db1;
    protected static $db2;

    protected $Code;
    protected $CodeVal;
    protected $Student;
    protected $Dummy;
    protected $db;
    
    
    public static function setUpBeforeClass()
    {
    
        Handler::get()->resetHandlers();
        $typ='dataBase';
        $name='test';
        $Code=get_called_class().'_1';
        $CodeVal=get_called_class().'_2';
        $Student=get_called_class().'_3';
        self::$db1=Handler::get()->getBase($typ, $name);
        Handler::get()->setStateHandler($Code, $typ, $name);
        Handler::get()->setStateHandler($CodeVal, $typ, $name);
        Handler::get()->setStateHandler($Student, $typ, $name);
        
        $typ='fileBase';
        $name=$name.'_f';
        $Code=get_called_class().'_f_1';
        $CodeVal=get_called_class().'_f_2';
        $Student=get_called_class().'_f_3';
        self::$db2=Handler::get()->getBase($typ, $name);
        Handler::get()->setStateHandler($Code, $typ, $name);
        Handler::get()->setStateHandler($CodeVal, $typ, $name);
        Handler::get()->setStateHandler($Student, $typ, $name);
    }
    
    public function setTyp($typ)
    {
        if ($typ== 'SQL') {
            $this->db=self::$db1;
            $this->Student=get_called_class().'_1';
            $this->Code=get_called_class().'_2';
            $this->CodeVal=get_called_class().'_3';
            $this->Dummy=get_called_class().'_4';
        } else {
            $this->db=self::$db2;
            $this->Student=get_called_class().'_f_1';
            $this->Code=get_called_class().'_f_2';
            $this->CodeVal=get_called_class().'_f_3';
            $this->Dummy=get_called_class().'_f_4';
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
    
        // Ref -CodeVal
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        
        $res= $codeval->deleteMod();
        $this->assertTrue($res);
        
        $path='/'.$this->Code;
        $res = $codeval->addAttr('ValueOf', Mtype::M_REF, $path);
        $this->assertTrue($res);

        $res = $codeval->addAttr('Label', Mtype::M_STRING);
        $this->assertTrue($res);
        
        $res = $codeval->saveMod();
        $this->assertTrue($res);

        $r = $codeval-> getErrLog();
        $r->show();
        $this->assertEquals($r->logSize(), 0);
        
        $this->assertTrue($codeval->checkMod());
                        
        // CRef -Code
        $code = new Model($this->Code);
        $this->assertNotNull($code);
    
        $res= $code->deleteMod();
        $this->assertTrue($res);
        
        $res = $code->addAttr('CodeName', Mtype::M_STRING);
        $this->assertTrue($res);

        $path='/'.$this->CodeVal.'/ValueOf';
        $res = $code->addAttr('Values', Mtype::M_CREF, $path);
        $this->assertTrue($res);

        $res = $code->addAttr('DefaultVal', Mtype::M_CODE, '/./Values');
        $this->assertTrue($res);
        
        $res = $code->saveMod();
        $this->assertTrue($res);

        $r = $code-> getErrLog();
        $this->assertEquals($r->logSize(), 0);
        
        // Code -Student
        $student = new Model($this->Student);
        $this->assertNotNull($student);

        $res= $student->deleteMod();
        $this->assertTrue($res);
        
        $res = $student->addAttr('Name', Mtype::M_STRING);
        $this->assertTrue($res);

        $path='/'.$this->Code.'/1/Values';
        $res = $student->addAttr('Sexe', Mtype::M_CODE, $path);
        $this->assertTrue($res);

        $path='/'.$this->CodeVal;
        $res = $student->addAttr('DSexe', Mtype::M_CODE, $path);
        $this->assertTrue($res);


        $res = $student->saveMod();
        $this->assertTrue($res);

        $this->assertFalse($student->checkMod());
        $this->assertEquals($student->getErrLine(), CstError::E_ERC007.':'.$this->Code.':1');
                
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

        $code->checkMod();
        echo $code->getErrLine();
        $this->assertTrue($code->checkMod());
        
        $res = $code->setVal('CodeName', 'Sexe');
        $this->assertTrue($res);
        
        $id = $code->save();
        $this->assertEquals($id, 1);

        $res = $code->getVal('Values');
        $this->assertEquals($res, []);

        $r = $code-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $student = new Model($this->Student);
        $this->assertNotNull($student);
        
        $res = $student->setVal('Sexe', 2);
        $this->assertFalse($res);

        $res = $student->setVal('DSexe', 2);
        $this->assertFalse($res);
        
        $db->commit();

        //  Male
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        
        $res = $codeval->setVal('Label', 'Male');
        $this->assertTrue($res);
        
        $res = $codeval->getValues('ValueOf');
        $this->assertEquals($res, [$id]);
        
        $res = $codeval->setVal('ValueOf', $id);
        $this->assertTrue($res);

        $res = $codeval->getRef('ValueOf');
        $this->assertEquals($res->getId(), $code->getId());
        $this->assertEquals($res->getModName(), $code->getModName());
        
        $res = $codeval->getModRef('ValueOf');
        $this->assertEquals($res, $this->Code);
                
        $id1= $codeval->save();
        $this->assertEquals($id1, 1);

        $res = $code->getVal('Values');
        $this->assertEquals($res, [$id1]);

        $r = $codeval-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();
        
        //Female
        
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        
        $res = $codeval->setVal('Label', 'Female');
        $this->assertTrue($res);

        $res = $codeval->getRef('ValueOf');
        $this->assertNull($res);

        $res = $codeval->setVal('ValueOf', $id);
        $this->assertTrue($res);
        
        $id2= $codeval->save();
        $this->assertEquals($id2, 2);

        $res = $code->getVal('Values');
        $this->assertEquals($res, [$id1,$id2]);

        $res = $code->getValues('DefaultVal');
        $this->assertEquals($res, [$id1,$id2]);

        $r = $codeval-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();
    }
    
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testNewCode
    */
    public function testNewCodeUse($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();

        // Student
        
        $student = new Model($this->Student);
        $this->assertNotNull($student);

        $student->checkMod();
        $student->getErrLog()->show();
        $this->assertTrue($student->checkMod());
        
        $res = $student->getValues('Sexe');
        $this->assertEquals($res, [1,2]);

        $res = $student->getValues('DSexe');
        $this->assertEquals($res, [1,2]);
        
        $res = $student->setVal('Name', 'Quoilin');
        $this->assertTrue($res);
        
        $res = $student->setVal('Sexe', null);
        $this->assertTrue($res);

        $res = $student->setVal('DSexe', null);
        $this->assertTrue($res);
        
        $res = $student->setVal('Sexe', 2);
        $this->assertTrue($res);

        $res = $student->setVal('DSexe', 2);
        $this->assertTrue($res);
        
        $res = $student->getCode('Sexe', 2);
        
        $codeval = new Model($this->CodeVal, 2);
        $codeval->protect('ValueOf');
                
        $this->assertEquals($codeval, $res);

        $res = $student->getCode('DSexe', 2);
        
        $codeval = new Model($this->CodeVal, 2);
                
        $this->assertEquals($codeval, $res);

        
        $id= $student->save();
        $this->assertEquals($id, 1);

        $r = $student-> getErrLog();
        $this->assertEquals($r->logSize(), 0);

        $db->commit();
    }
    
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testNewCodeUse
    */
    public function testErrors($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();

        $student = new Model($this->Student);
        $this->assertNotNull($student);
        $log = $student->getErrLog();
        
        $this->assertFalse($student->getErrLine());

        $res = $student->addAttr('xxx', Mtype::M_CODE);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC008.':xxx:'.Mtype::M_CODE);
        
        $res = $student->addAttr('xxx', Mtype::M_CODE, '/a/1/b/2');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':xxx:/a/1/b/2');
                
        $res = $student->addAttr('xxx', Mtype::M_CODE, 'xx');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':xxx:xx');
        

        $code = new Model($this->Code);
        $this->assertNotNull($code);
        $log = $code->getErrLog();
        
        $res = $code->setVal('Values', 'xx');
        $this->assertFalse($res);
        $this->assertEquals($log->getLine(0), CstError::E_ERC013.':Values');
                        
        $codeval = new Model($this->CodeVal);
        $this->assertNotNull($codeval);
        $log = $codeval->getErrLog();
        
        $res = $codeval->setVal('ValueOf', 1000);
        $this->assertFalse($res);
        $this->assertEquals($log->getLine(0), CstError::E_ERC007.':'.$this->Code.':1000');
    
        $log = $student->getErrLog();
        $res = $student->setVal('Sexe', 1000);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC016.':Sexe:1000');
        
        $res = $student->getModRef('notexists');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');

        $res = $student->protect('notexists');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');

        $res = $student->isProtected('notexists');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');
        
        $res = $student->getModRef('Sexe');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC026.':Sexe');

        try {
            $res = $student->getRef('notexists');
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC002.':notexists');
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');

        try {
            $res = $student->newCref('notexists');
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC002.':notexists');
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');
        
        $res = $student->getValues('notexists');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');

        $res = $student->getValues('Name');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC015.':Name:'.Mtype::M_STRING);
    
        try {
            $res = $student->getCref('notexists', 1);
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC002.':notexists');
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');

        try {
            $res = $student->isOneCref('notexists');
        } catch (Exception $e) {
            $r= $e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC002.':notexists');
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');
        
        try {
            $res = $student->getCref('Sexe', 1);
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC027.':Sexe');
        $this->assertEquals($student->getErrLine(), CstError::E_ERC027.':Sexe');
    
        $res = $student->getCode('notexists', 1);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC002.':notexists');

        $res = $student->getCode('Name', 1);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC028.':Name');
        
        $res = $student->addAttr($this->Dummy, Mtype::M_REF, '/XXX/CC');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':'.$this->Dummy.':/XXX/CC');

        $res = $student->addAttr($this->Dummy, Mtype::M_REF, '/'.$this->Dummy);
        $this->assertTrue($res);
        $res=$student->checkMod();
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC014.':'.$this->Dummy.':'.Mtype::M_REF.':/'.$this->Dummy);

        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, '/'.$this->Dummy);
        $this->assertTrue($res);
        $res=$student->checkMod();
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC014.':'.$this->Dummy.':'.Mtype::M_CODE.':/'.$this->Dummy);

        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, '/'.$this->Dummy.'/x');
        $this->assertTrue($res);
        $res=$student->checkMod();
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':'.$this->Dummy.':/'.$this->Dummy.'/x');

        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, '/./Name');
        $this->assertTrue($res);
        $res=$student->checkMod();
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC055.':Name');

        $path='/'.$this->Code.'/1/CodeName';
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, $path);
        $this->assertTrue($res);
        $res=$student->checkMod();
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC055.':CodeName');

        
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CREF, '/XXX/CC/TT');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':'.$this->Dummy.':/XXX/CC/TT');
        
        $res = $student->addAttr($this->Dummy, Mtype::M_CREF, '/'.$this->Dummy.'/X');
        $this->assertTrue($res);
        $res=$student->checkMod();
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC014.':'.$this->Dummy.':'.Mtype::M_CREF.':/'.$this->Dummy.'/X');
        
        
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CREF, '/'.$this->Code.'/CodeName');
        $this->assertTrue($res);
        $res=$student->checkMod();
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC054.':CodeName');
//
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_REF, '/'.$this->Dummy);
        $this->assertTrue($res);
        try {
            $res = $student->getValues($this->Dummy);
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC017.':'.$this->Dummy);

        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CREF, '/'.$this->Dummy.'/X');
        $this->assertTrue($res);
        try {
            $res = $student->getVal($this->Dummy);
        } catch (Exception $e) {
            $res=$e->getMessage();
        }
        $this->assertEquals($res, CstError::E_ERC017.':'.$this->Dummy);
        
        $db->commit();
    }
    
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testErrors
    */
    public function testDel($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();

        $student = new Model($this->Student);
        $res = $student->protect('Sexe');
        $this->assertTrue($res);
        
        $res = $student->setVal('Sexe', 1);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC034.':Sexe');

        
        $res=$student->delAttr('Sexe');
        $this->assertTrue($res);
        $db->commit();
    }
}
