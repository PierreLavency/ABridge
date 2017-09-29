<?php

use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\CModel;

class ModUtils_Test_dataBase_Prof extends CModel
{
	public function initMod($bindings)
	{
		$this->mod->addAttr('AttrX',Mtype::M_REF,'x');
	}
}

class ModUtils_Test_fileBase_Prof extends CModel
{
	public function initMod($bindings)
	{
		$this->mod->addAttr('AttrX',Mtype::M_REF,'x');
	}
}

class ModUtils_Test_memBase_Prof extends CModel
{
	public function initMod($bindings)
	{
		$this->mod->addAttr('AttrX',Mtype::M_REF,'x');
	}
}

class ModUtils_Test extends PHPUnit_Framework_TestCase
{
    protected static $dbs;
    protected static $prm;
    

    protected $Student;
    protected $Prof;
    protected $Dummy;
    protected $db;
    
    public static function setUpBeforeClass()
    {
        $classes = ['Student','Prof'];
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
        $this->Prof=self::$prm[$typ]['Prof'];
        $this->Dummy=get_called_class().'_'.$typ.'_Dummy';
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
        
        $student = new Model($this->Student);
        
        $res= $student->deleteMod();

        $res = $student->addAttr('Name', Mtype::M_STRING);
        $res = $student->addAttr('Ref', Mtype::M_REF, '/'.$this->Student);
        $res = $student->addAttr('Code', Mtype::M_CODE, '/'.$this->Student);
        $res = $student->addAttr('Cref', Mtype::M_CREF, '/'.$this->Student.'/Ref');
        
        $res = $student->saveMod();
        $this->assertTrue($res);
        
        $this->assertTrue(ModUtils::checkMod($student));

        $res = $student->save();
        $this->assertEquals(1, $res);
        
        $db->commit();
    }
    /**
     * @dataProvider Provider1
     *
     /**
     * @depends testSaveMod
     */
    public function testErr($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $student = new Model($this->Student);
        
        $res = $student->addAttr($this->Dummy, Mtype::M_REF, '/'.$this->Dummy);
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC014.':'.$this->Dummy.':'.Mtype::M_REF.':'.$this->Dummy);

        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_STRING);
        $res= $student->setProp($this->Dummy, Model::P_EVL);
        $this->assertTrue($res);
        $this->assertTrue($student->isProp($this->Dummy, Model::P_EVL));
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC061.':'.$this->Dummy);
        
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, '/'.$this->Dummy);
        $this->assertTrue($res);
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC014.':'.$this->Dummy.':'.Mtype::M_CODE.':'.$this->Dummy);
        
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, '/'.$this->Dummy.'/x');
        $this->assertTrue($res);
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':'.$this->Dummy.':'.$this->Dummy);
        
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, '/./Name');
        $this->assertTrue($res);
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC055.':Name');
        
        $res =$student->delAttr($this->Dummy);
        $path='/'.$this->Student.'/1/Name';
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, $path);
        $this->assertTrue($res);
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC055.':Name');
        
        
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CREF, '/XXX/CC/TT');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':'.$this->Dummy.':/XXX/CC/TT');
        
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_REF, '/XXX/CC/TT');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':'.$this->Dummy.':/XXX/CC/TT');

        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CODE, '/XXX/CC/TT/GG');
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC020.':'.$this->Dummy.':/XXX/CC/TT/GG');
        
        $res = $student->addAttr($this->Dummy, Mtype::M_CREF, '/'.$this->Dummy.'/X');
        $this->assertTrue($res);
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC014.':'.$this->Dummy.':'.Mtype::M_CREF.':'.$this->Dummy);
                
        $res =$student->delAttr($this->Dummy);
        $res = $student->addAttr($this->Dummy, Mtype::M_CREF, '/'.$this->Student.'/Name');
        $this->assertTrue($res);
        $res=ModUtils::checkMod($student);
        $this->assertFalse($res);
        $this->assertEquals($student->getErrLine(), CstError::E_ERC054.':Name');
        
        $student->saveMod();
        $this->assertFalse(ModUtils::checkMods([$this->Student], [$this->Student=>$this->Student], false));
        
        $output = "\nLINE:0\n".CstError::E_ERC054.':Name'."\n\n";
        $this->expectOutputString($output);
        $this->assertFalse(ModUtils::checkMods([$this->Student], [$this->Student=>$this->Student], true));
        
        $output2="\nLINE:0\n".CstError::E_ERC020.':AttrX:x'."\n\n";
        $this->expectOutputString($output.$output2);
        $this->assertFalse(ModUtils::initModBinding($this->Prof, [$this->Prof=>$this->Prof], true));
        
        $this->assertFalse(ModUtils::initModBindings([$this->Prof=>$this->Prof]));
        $db->commit();
    }
    
    
    /**
     * @dataProvider Provider2
     */
    
    public function testnorm($a, $expected)
    {
        $this->assertEquals($expected, ModUtils::normBindings($a));
    }
    
    public function Provider2()
    {
        return [
                [['X'],['X'=>'X']],
                [[],[]],
                [['X'=>'X'],['X'=>'X']],
        ];
    }
    

}
