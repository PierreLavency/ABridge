<?php
    
/* */

use ABridge\ABridge\Mod\FileBase;

require_once("Base_Case.php");

class FileBase_Test extends Base_Case
{
    
    public static function setUpBeforeClass()
    {
        
        self::$CName=get_called_class().'_f_1';
        self::$CName2=get_called_class().'_f_2';
        self::$DBName= 'atest';
        $fpath='C:/Users/pierr/ABridge/Datastore/';
        self::$db = new FileBase($fpath,self::$DBName);
        
        if (self::$db->existsMod(self::$CName)) {
            self::$db ->delMod(self::$CName);
        }
        if (self::$db ->existsMod(self::$CName2)) {
            self::$db ->delMod(self::$CName2);
        }
        
        self::$db ->commit();
    }
}
