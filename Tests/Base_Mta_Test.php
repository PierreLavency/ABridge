<?php
    
/* */

require_once("FileBase.php"); 
require_once("SqlBase.php"); 

class FileBase_Mta_Test extends PHPUnit_Framework_TestCase {


    public function testFile() 
    {
		$name = 'notexists';
		$this->assertFalse(FileBase::exists($name));
		$x = new FileBase($name,'cl822','cl822');
		$this->assertNotNull($x);
		$x->commit();
		$this->assertTrue(FileBase::exists($name));
		$res=$x->remove();
		$this->assertTrue($res);
		$this->assertFalse(FileBase::exists($name));
		
    }

     public function testSql() 
    {
		$name = 'notexists';
		$this->assertFalse(SqlBase::exists($name));
		$x = new SqlBase($name,'cl822','cl822');
		$this->assertNotNull($x);
		$x->commit();
		$this->assertTrue(SqlBase::exists($name));
		$res=$x->remove();
		$this->assertTrue($res);
		$this->assertFalse(SqlBase::exists($name));
		
		$r=false;
        try {$x = new SqlBase($name,'cl823','cl823');} catch (Exception $e) {$r = true;}
        $this->assertTrue($r);      

    }

}


?>  