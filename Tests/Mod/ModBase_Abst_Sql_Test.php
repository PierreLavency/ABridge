<?php

use ABridge\ABridge\Mod\SQLBase;
use ABridge\ABridge\UtilsC;

require_once("ModBase_Abst_Case.php");

class ModBase_Abst_Sql_Test extends ModBase_Abst_Case
{
    public static function setUpBeforeClass()
    {
        $classes = ['test1','test2'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['dataBase']);
        
        self::$CName= $prm['dataBase']['test1'];
        
        self::$HName=$prm['dataBase']['test2'];
        
        self::$DBName=$prm['application']['dataBase'];
        
        self::$db = new SQLBase(
            $prm['application']['path'],
            $prm['application']['host'],
            $prm['application']['user'],
            $prm['application']['pass'],
            self::$DBName
        );
    }
}
