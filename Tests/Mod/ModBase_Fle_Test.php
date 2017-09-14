<?php
    
use ABridge\ABridge\Mod\FileBase;
use ABridge\ABridge\UtilsC;

require_once 'ModBase_Case.php';

class ModBase_Fle_Test extends ModBase_Case
{

    public static function setUpBeforeClass()
    {
        $classes = ['test1'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        
        self::$CName= $prm['fileBase']['test1'];
        
        self::$DBName=$prm['application']['flnm'];
        $fpath=$prm['application']['path'];
        
        self::$db = new FileBase($fpath, self::$DBName);
    }
}
