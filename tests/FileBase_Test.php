<?php
    
/* */

require_once("FileBase.php"); 
require_once("Base_Case.php");

class FileBase_Test extends Base_Case {
    
    public static function setUpBeforeClass()
    {   
        self::$CName='ERC';
        self::$CName2='ERCode';
        self::$DBName= 'test';
        
        self::$CName='ERC';
        self::$CName2='ERCode';
        self::$DBName= 'atest';
        self::$db = new FileBase(self::$DBName);
        
        if (self::$db->existsMod(self::$CName)) {self::$db ->delMod(self::$CName);}
        if (self::$db ->existsMod(self::$CName2)){self::$db ->delMod(self::$CName2);}
        
        self::$db ->commit();
    }

}


?>  