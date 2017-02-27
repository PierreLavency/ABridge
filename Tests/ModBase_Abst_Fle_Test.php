<?php
	
require_once("FileBase.php"); 
require_once("ModBase_Abst_Case.php"); 

class ModBase_Abst_Fle_Test extends ModBase_Abst_Case  
{

	public static function setUpBeforeClass()
	{		
		self::$CName='Student';
		self::$HName='Child';
		self::$DBName= 'atest';
		self::$db = new FileBase(self::$DBName);
	}
}

