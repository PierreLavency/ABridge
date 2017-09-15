<?php

use ABridge\ABridge\Mod\SQLBase;

require_once("ModBase_Case.php");
use ABridge\ABridge\UtilsC;

class ModBase_Sql_Test extends ModBase_Case
{
    public static function setUpBeforeClass()
    {
        $classes = ['test1'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['dataBase']);
        
        self::$CName= $prm['dataBase']['test1'];
        
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
