<?php
	
require_once("FileBase.php"); 
require_once("ModBase_Case.php"); 

class ModBase_Fle_Test extends ModBase_Case  
{

	public static function setUpBeforeClass()
	{		
		self::$CName='Student';
		self::$DBName= 'atest';
		self::$db = new FileBase(self::$DBName,'cl822','cl822');
	}
}


?>	