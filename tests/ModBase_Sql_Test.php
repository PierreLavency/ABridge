<?php

require_once("Src\SQLBase.php"); 
require_once("ModBase_Case.php"); 

class ModBase_Sql_Test extends ModBase_Case  
{
	public static function setUpBeforeClass()
		{		
			self::$CName='Student';
			self::$DBName= 'test';
			self::$db = new SQLBase(self::$DBName,'cl822','cl822');
		}

}

?>	