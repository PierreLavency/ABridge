<?php
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;

class Model_Evp_Test_dataBase_Student extends CModel
{
    private $_fsave = true;
    protected $_fdel  = true;
    
    public function initMod($bindings)
    {
        return true;
    }
    
    public function save()
    {
        $a = $this->mod->getValN('a');
        $b = $this->mod->getValN('b');
        $this->mod->setValN('aplusb', $a+$b);
        if (!$a and $this->mod->getId() and $this->_fsave) {
            $this->mod->getErrLog()->logLine('wrong');
            $this->_fsave = false;
            return false;
        }
        $res =  parent::save();
        if (!$this->_fsave) {
            $this->mod->getErrLog()->logLine('Awrong');
            return false;
        }
        return $res;
    }
    
    public function delet()
    {
        $a = $this->mod->getValN('a');
        if (!$a and $this->mod->getId() and $this->_fdel) {
            $this->mod->getErrLog()->logLine('Dwrong');
            $this->_fdel = false;
            return false;
        }
        $res =  parent::delet();
        if (!$this->_fdel) {
            $this->mod->getErrLog()->logLine('DDwrong');
            return false;
        }
        return  $res;
    }
    
    public function testN()
    {
        return 'Model_Evp_Test_dataBase_Student';
    }
}
class Model_Evp_Test_fileBase_Student extends CModel
{

    private $_x;
    private $_fsave = true;
    private $_fdel  = true;
    
    public function initMod($bindings)
    {
        return true;
    }
    
    public function save()
    {
        $a = $this->mod->getValN('a');
        $b = $this->mod->getValN('b');
        $this->mod->setVal('aplusb', $a+$b);
        if (!$a and $this->mod->getId()and $this->_fsave) {
            $this->mod->getErrLog()->logLine('wrong');
            $this->_fsave = false;
            return false;
        }
        $res =  $this->mod->saveN();
        if (!$this->_fsave) {
            $this->mod->getErrLog()->logLine('Awrong');
            return false;
        }
        return $res;
    }
    
    public function delet()
    {
        $a = $this->mod->getValN('a');
        if (!$a and $this->mod->getId()and $this->_fdel) {
            $this->mod->getErrLog()->logLine('Dwrong');
            $this->_fdel = false;
            return false;
        }
        $res= $this->mod->deletN();
        if (!$this->_fdel) {
            $this->mod->getErrLog()->logLine('DDwrong');
            return false;
        }
        return $res;
    }
    
    public function testN()
    {
        return 'Model_Evp_Test_fileBase_Student';
    }
}

class Model_Evp_Test_memBase_Student extends CModel
{
    
    private $_x;
    private $_fsave = true;
    private $_fdel  = true;

    public function initMod($bindings)
    {
        return true;
    }
    
    public function save()
    {
        $a = $this->mod->getValN('a');
        $b = $this->mod->getValN('b');
        $this->mod->setVal('aplusb', $a+$b);
        if (!$a and $this->mod->getId()and $this->_fsave) {
            $this->mod->getErrLog()->logLine('wrong');
            $this->_fsave = false;
            return false;
        }
        $res =  $this->mod->saveN();
        if (!$this->_fsave) {
            $this->mod->getErrLog()->logLine('Awrong');
            return false;
        }
        return $res;
    }
    
    public function delet()
    {
        $a = $this->mod->getValN('a');
        if (!$a and $this->mod->getId()and $this->_fdel) {
            $this->mod->getErrLog()->logLine('Dwrong');
            $this->_fdel = false;
            return false;
        }
        $res= $this->mod->deletN();
        if (!$this->_fdel) {
            $this->mod->getErrLog()->logLine('DDwrong');
            return false;
        }
        return $res;
    }
    
    public function testN()
    {
        return 'Model_Evp_Test_memBase_Student' ;
    }
}
class Model_Evp_Test extends PHPUnit_Framework_TestCase
{
    protected static $dbs;
    protected static $prm;

    protected $Student='testevalP';
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
        $this->Student=self::$prm[$typ]['Student'];
    }
    
    public function Provider1()
    {
        return [['dataBase'],['fileBase'],['memBase']];
    }
    /**
     * @dataProvider Provider1
     */

    public function testNew($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $x = new Model($this->Student);
        $res=$x->deleteMod();
        $this->assertTrue($res);
        
        $x->addAttr('a', Mtype::M_INT);
        $x->addAttr('b', Mtype::M_INT);
        $x->addAttr('aplusb', Mtype::M_INT);
        $x->setProp('aplusb', Model::P_EVL);
 
        $cname = $this->Student;
        $x->addAttr('c', Mtype::M_CODE, "/$cname");
        $x->addAttr('tmp', Mtype::M_INT);
        $x->setProp('tmp', Model::P_TMP);
        
        $this->assertFalse($x->isErr());

        $res = $x->saveMod();
        $this->assertTrue($res);
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends testNew
    */
    public function testsave($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $x = new Model($this->Student);

        $x->setVal('a', 1);
        $x->setVal('b', 1);
        $x->setVal('tmp', 1);
        
        $res=$x->save();
        $this->assertEquals(1, $res);
        
        $x = new Model($this->Student);
        $x->setVal('a', 1);
        $x->setVal('b', 1);
        
        $res=$x->save();
        $this->assertEquals(2, $res);
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends  testsave
    */
    
    public function testget($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $x = new Model($this->Student, 1);
        $res= $x->getVal('aplusb');
        $this->assertEquals(2, $res);
        
        $res=$x->isOptl('aplusb');
        $this->assertFalse($res);

        $res=$x->isSelect('aplusb');
        $this->assertTrue($res);

        $res=$x->isProp('aplusb', Model::P_TMP);
        $this->assertFalse($res);
            
        $res=$x->isProp('tmp', Model::P_TMP);
        $this->assertTrue($res);
        
        $res=$x->getVal('tmp');
        $this->assertNull($res);
        
        $this->assertEquals(2, count($x->getValues('c')));
        $this->assertTrue($x->initMod([]));
        $cobj = $x->getCobj();
        $this->assertEquals($this->Student, $cobj->testN());

        $y= new Model('notExists');

        try {
            $y->initMod([]);
        } catch (Exception $e) {
            $res= $e->getMessage();
        }
        $this->assertEquals(CstError::E_ERC061.':notExists', $res);
        $db->commit();
    }
    
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends  testget
    */
    public function testerr($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $this->assertNotNull($x = new Model($this->Student, 1));
        $res= $x->setVal('aplusb', 1);
        $res= $x->setVal('a', 1);
        $this->assertTrue($res);
        
        $this->assertEquals($x->getErrLine(), CstError::E_ERC042.':'.'aplusb');
        
        $res=$x->isSelect('xx');
        $this->assertFalse($res);
        $this->assertEquals($x->getErrLine(), CstError::E_ERC002.':'.'xx');

        $res=$x->isModif('xx');
        $this->assertFalse($res);
        $this->assertEquals($x->getErrLine(), CstError::E_ERC002.':'.'xx');
        
        $res= $x->setVal('a', 0);
        $this->assertTrue($res);
        
        $res = $x->save();
        $this->assertFalse($res);
        $this->assertEquals($x->getErrLine(), 'wrong');

        $res = $x->save();
        $this->assertFalse($res);
        $this->assertEquals($x->getErrLine(), 'Awrong');


        $this->assertNotNull($x = new Model($this->Student, 2));

        $res= $x->setVal('a', 0);
        $this->assertTrue($res);
        
        $res = $x->delet();
        $this->assertFalse($res);
        $this->assertEquals($x->getErrLine(), 'Dwrong');

                
        $res = $x->delet();
        $this->assertFalse($res);
        $this->assertEquals($x->getErrLine(), 'DDwrong');
        
        $db->commit();
    }
    
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends  testerr
    */
    public function testdel($typ)
    {
        
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        $this->assertNotNull($x = new Model($this->Student, 1));

        $res= $x->setVal('a', 1);
        $this->assertTrue($res);
        
        $res= $x->delAttr('aplusb');
        $this->assertTrue($res);
 
        $res= $x->delAttr('tmp');
        $this->assertTrue($res);

        $res= $x->delet();

        $this->assertTrue($res);
        $db->commit();
    }
}
