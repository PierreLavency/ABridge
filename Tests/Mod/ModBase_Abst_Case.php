<?php
    
/* */
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\ModBase;
use ABridge\ABridge\Mod\Mtype;

class ModBase_Abst_Case extends PHPUnit_Framework_TestCase
{

    protected static $CName;
    protected static $HName;
    protected static $DBName;
    protected static $db;
    
    protected $hdlr;

    public function testSaveMod()
    {
        $db=self::$db;
        $db->beginTrans();

        $this->assertNotNull($sh = new ModBase($db));
        
        $this->assertNotNull($mod = new Model(self::$HName));
        $this->assertTrue($sh->eraseMod($mod));

        $this->assertNotNull($mod = new Model(self::$CName));
        $this->assertTrue($sh->eraseMod($mod));
                    
        $this->assertTrue($mod->addAttr('Name', Mtype::M_STRING));
        $this->assertTrue($mod->addAttr('Surname', Mtype::M_STRING));
        $this->assertTrue($mod->setAbstr());
        $this->assertTrue($sh->saveMod($mod));

        $this->hdlr = $sh;
        
        $db->commit();
    }

    /**
    * @depends testSaveMod
    */
    public function testrestoreMod()
    {
        $db=self::$db;
        $db->beginTrans();

        $this->assertNotNull($sh = new ModBase($db));
        $this->assertNotNull($mod = new Model(self::$CName));
        $this->assertTrue($sh->restoreMod($mod));
        $this->assertTrue($mod->existsAttr('Name'));
        $this->assertTrue($mod->existsAttr('Surname'));
        
        $this->assertTrue($mod->delAttr('Surname'));
        $this->assertTrue($mod->addAttr('Age', Mtype::M_STRING));
        $this->assertTrue($sh->saveMod($mod));
        
        $this->assertNotNull($mod = new Model(self::$HName));
        $this->assertFalse($mod->setInhNme(self::$CName));
        $this->assertTrue($mod->addAttr('Sexe', Mtype::M_STRING));
        $this->assertTrue($sh->saveMod($mod));


        $db->commit();
    }

    /**
    * @depends testrestoreMod
    */
    public function testrestoreMod1()
    {
        $db=self::$db;
        $db->beginTrans();
        $this->assertNotNull($sh = new ModBase($db));

        $this->assertNotNull($mod = new Model(self::$HName));
        $this->assertTrue($sh->restoreMod($mod));
        $this->assertTrue($mod->existsAttr('Sexe'));
        
        $this->assertNotNull($mod = new Model(self::$CName));
        $this->assertTrue($sh->restoreMod($mod));
        $this->assertTrue($mod->existsAttr('Name'));
        $this->assertFalse($mod->existsAttr('Surname'));
        $this->assertTrue($mod->existsAttr('Age'));
        
        $this->assertTrue($mod->addAttr('Surname', Mtype::M_STRING));
        $this->assertTrue($mod->delAttr('Age'));
        $this->assertTrue($sh->saveMod($mod));
  
        $this->assertNotNull($mod = new Model(self::$HName));
        $this->assertTrue($sh->restoreMod($mod));
        $this->assertTrue($mod->existsAttr('Sexe'));
            
        $db->commit();
    }
    
    /**
    * @depends testrestoreMod1
    */

    public function testSaveObj()
    {
        $db=self::$db;
        $db->beginTrans();
        $this->assertNotNull($sh = new ModBase($db));
        $this->assertNotNull($mod = new Model(self::$CName));
        $this->assertTrue($sh->restoreMod($mod));
        $this->assertFalse($mod->existsAttr('Age'));
    
        $db->commit();
    }
}
