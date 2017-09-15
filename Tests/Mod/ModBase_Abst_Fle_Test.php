<?php
    
use ABridge\ABridge\Mod\FileBase;
use ABridge\ABridge\UtilsC;

require_once("ModBase_Abst_Case.php");

class ModBase_Abst_Fle_Test extends ModBase_Abst_Case
{

    public static function setUpBeforeClass()
    {
        $classes = ['test1','test2'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        
        self::$CName= $prm['fileBase']['test1'];
        self::$HName= $prm['fileBase']['test2'];
        
        self::$DBName=$prm['application']['fileBase'];
        $fpath=$prm['application']['path'];
        
        self::$db = new FileBase($fpath, self::$DBName);
    }
}
