<?php
	
/* */

require_once("Src\SQLBase.php");
require_once("Base_Case.php");

class SQLBase_Test extends Base_Case {

	public static function setUpBeforeClass()
    {
		
		self::$CName='ERC';
		self::$CName2='ERCode';
		self::$DBName= 'test';
        self::$db = new SQLBase(self::$DBName,'cl822','cl822');
		
		self::$db ->delMod(self::$CName);
		self::$db ->delMod(self::$CName2);
		
		self::$db ->commit();
		
    }
}
?>	