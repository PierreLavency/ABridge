<?php
    
/* */

use ABridge\ABridge\Mod\FileBase;
use ABridge\ABridge\Mod\SqlBase;

class Base_Rmv_Test extends PHPUnit_Framework_TestCase
{

    public function testFile()
    {
        $fpath = 'C:/Users/pierr/ABridge/Datastore/';
       
        $name = 'notexists';
        $this->assertFalse(FileBase::exists($fpath, $name));
        $x = new FileBase($fpath, $name);
        $this->assertNotNull($x);
        $x->commit();
        $this->assertTrue(FileBase::exists($fpath, $name));
 
        $res=$x->remove();
        $this->assertTrue($res);
        $this->assertFalse(FileBase::exists($fpath, $name));
    }

    public function testSql()
    {
        $prm=[
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];

        
        $name = 'notexists';
        $this->assertFalse(SqlBase::exists($prm['path'], $name));
        $x = new SQLBase(
            $prm['path'],
            $prm['host'],
            $prm['user'],
            $prm['pass'],
            $name
        );
        $this->assertNotNull($x);
        $x->commit();
        $this->assertTrue(SqlBase::exists($prm['path'], $name));

        $res=$x->remove();
        $this->assertTrue($res);
        $this->assertFalse(SqlBase::exists($prm['path'], $name));
        
        $r=false;
        try {
            $x = new SQLBase(
                $prm['path'],
                $prm['host'],
                'cl823',
                'cl823',
                $name
            );
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);

  
        $r=false;
        try {
            $x = new SQLBase(
                $prm['path'],
                $prm['host'],
                'noexist',
                'noexist',
                $name
            );
        } catch (Exception $e) {
            $r = true;
        }
        $this->assertTrue($r);
    }
}