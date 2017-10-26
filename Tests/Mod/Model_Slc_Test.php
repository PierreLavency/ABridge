<?php
    

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\UtilsC;

class Model_Slc_Test extends PHPUnit_Framework_TestCase
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
        };
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

    public function testSelect($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $this->assertNotNull($x = new Model($this->Student));
        $res=$x->deleteMod();
            $x->getErrLog()->show();
        $this->assertTrue($res);

        $x->addAttr('a', Mtype::M_STRING);
        $x->addAttr('b', Mtype::M_INT);
        $x->addAttr('c', Mtype::M_DATE);
        
        $x->getErrLog()->show();
        $this->assertFalse($x->isErr());

        $res = $x->saveMod();
        $this->assertTrue($res);
        
        $n = 20;
        for ($i=0; $i<$n; $i++) {
            $x = new Model($this->Student);
            $a = 'Name_'. $i;
            $x->setVal('a', $a);
            $x->setVal('b', $i);

            $x->setVal('c', '1959-05-26');
            $id=$x->save();
            $x->getErrLog()->show();
            $this->assertFalse($x->isErr());
        }

        $this->assertTrue($x->setCriteria(['a','b','c'], [], ['Name_1',1,'1959-05-26'], []));
        $this->assertEquals(1, count($x->select()));
        $this->assertTrue($x->setCriteria(['a'], ['a'=>'>'], ['Name_1'], []));
        $this->assertEquals(18, count($x->select()));
        $this->assertTrue($x->setCriteria(['b'], ['b'=>'>'], [1], []));
        $this->assertEquals(18, count($x->select()));
        $this->assertTrue($x->setCriteria(['c'], ['b'=>'>'], ['1959-05-26'], []));
        $this->assertEquals(20, count($x->select()));
        $x->protect('a');
        $this->assertEquals(1, count($x->select()));
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
    /**
    * @depends  testSelect
    */

    public function testerr($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $x = new Model($this->Student, 1);
        $this->assertFalse($x->setCriteria(['a','b','xx'], [], ['Name_1',1,'1959-05-26'], []));
        $this->assertEquals($x->getErrLine(), CstError::E_ERC002.':'.'xx');
        
        $db->commit();
    }
}
