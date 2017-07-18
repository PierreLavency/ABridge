<?php
    
/* */
use ABridge\ABridge\Mod\Base;
use ABridge\ABridge\Mod\FileBase;
use ABridge\ABridge\Mod\SqlBase;

class Base_Mta_Test extends PHPUnit_Framework_TestCase
{

    public function testFile()
    {
        $path = FileBase::getPath();
        $this->assertEquals('C:/Users/pierr/ABridge/Datastore/', $path);
        
        $name = 'notexists';
        $this->assertFalse(FileBase::exists($name));
        $x = new FileBase($name);
        $this->assertNotNull($x);
        $x->commit();
        $this->assertTrue(FileBase::exists($name));
        
        FileBase::setPath($path.'fileBase/');
        $this->assertFalse(FileBase::exists($name));
        FileBase::setPath($path);
        $this->assertTrue(FileBase::exists($name));
        
        $res=$x->remove();
        $this->assertTrue($res);
        $this->assertFalse(FileBase::exists($name));
    }

    public function testSql()
    {
        $path = SqlBase::getPath();
        $this->assertEquals('C:/Users/pierr/ABridge/Datastore/', $path);
        
        $name = 'notexists';
        $this->assertFalse(SqlBase::exists($name));
        $x = new SqlBase($name);
        $this->assertNotNull($x);
        $x->commit();
        $this->assertTrue(SqlBase::exists($name));
        
        Base::setPath($path.'fileBase/');
        $this->assertFalse(SqlBase::exists($name));
        SqlBase::setPath($path);
        $this->assertTrue(SqlBase::exists($name));
        
        $res=$x->remove();
        $this->assertTrue($res);
        $this->assertFalse(SqlBase::exists($name));
        
        $res= SqlBase::getDB();
        $this->assertEquals(3, count($res));
        $tres = SqlBase::setDB($res[0], 'cl823', 'cl823');
        
        $this->assertTrue($tres);
        
        $r=false;
        try {
            $x = new SqlBase($name);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);

        $tres = SqlBase::setDB($res[0], 'noexist', 'noexist');
        $this->assertTrue($tres);

        $r=false;
        try {
            $x = new SqlBase($name);
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
        
        $tres = SqlBase::setDB($res[0], $res[1], $res[2]);
        $this->assertTrue($tres);
    }
}
