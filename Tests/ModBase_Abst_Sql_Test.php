<?php

require_once("SQLBase.php"); 
require_once("ModBase_Abst_Case.php"); 

class ModBase_Abst_Sql_Test extends ModBase_Abst_Case  
{
	public static function setUpBeforeClass()
		{		
			self::$CName='Student';
			self::$HName='Child';
			self::$DBName= 'test';
			self::$db = new SQLBase(self::$DBName);
		}

}

?>	