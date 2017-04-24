<?php
    
/* */

require_once("FileBase.php"); 
require_once("Base_Case.php");

class FileBase_Test extends Base_Case {
    
    public static function setUpBeforeClass()
    {   
        
        self::$CName=get_called_class().'_f_1';
        self::$CName2=get_called_class().'_f_2';
        self::$DBName= 'atest';
        self::$db = new FileBase(self::$DBName);
        
        if (self::$db->existsMod(self::$CName)) {self::$db ->delMod(self::$CName);}
        if (self::$db ->existsMod(self::$CName2)){self::$db ->delMod(self::$CName2);}
        
        self::$db ->commit();
    }

}


?>  