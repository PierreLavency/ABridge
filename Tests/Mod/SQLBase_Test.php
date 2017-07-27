<?php
    
/* */

use ABridge\ABridge\Mod\SQLBase;

require_once("Base_Case.php");

class SQLBase_Test extends Base_Case
{

    public static function setUpBeforeClass()
    {
        
        self::$CName=get_called_class().'_1';
        self::$CName2=get_called_class().'_2';
        self::$DBName= 'test';
        $fpath='C:/Users/pierr/ABridge/Datastore/';
        
        self::$db = new SQLBase($fpath,'localhost','cl822','cl822',self::$DBName);
        
        self::$db ->delMod(self::$CName);
        self::$db ->delMod(self::$CName2);
        
        self::$db ->commit();
    }
}
