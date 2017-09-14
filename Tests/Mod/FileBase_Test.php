<?php
    
/* */

use ABridge\ABridge\Mod\FileBase;
use ABridge\ABridge\UtilsC;

require_once("Base_Case.php");

class FileBase_Test extends Base_Case
{
    
    public static function setUpBeforeClass()
    {
        $classes = ['test1','test2'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        
        self::$CName= $prm['fileBase']['test1'];
        self::$CName2=$prm['fileBase']['test2'];

        self::$DBName=$prm['application']['flnm'];
        $fpath=$prm['application']['path'];
            
        self::$db = new FileBase($fpath, self::$DBName);
        
        if (self::$db->existsMod(self::$CName)) {
            self::$db ->delMod(self::$CName);
        }
        if (self::$db ->existsMod(self::$CName2)) {
            self::$db ->delMod(self::$CName2);
        }
        
        self::$db ->commit();
    }
}
