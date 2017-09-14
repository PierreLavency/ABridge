<?php
    
/* */

use ABridge\ABridge\Mod\SQLBase;
use ABridge\ABridge\UtilsC;

require_once("Base_Case.php");

class SQLBase_Test extends Base_Case
{

    public static function setUpBeforeClass()
    {
        $classes = ['test1','test2'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['dataBase']);
        
        self::$CName= $prm['dataBase']['test1'];
        self::$CName2=$prm['dataBase']['test2'];
        
        self::$DBName=$prm['application']['dbnm'];
        
        self::$db = new SQLBase(
            $prm['application']['path'],
            $prm['application']['host'],
            $prm['application']['user'],
            $prm['application']['pass'],
            self::$DBName
        );
        
        self::$db ->delMod(self::$CName);
        self::$db ->delMod(self::$CName2);
        
        self::$db ->commit();
    }
}
