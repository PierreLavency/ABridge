<?php
    
use ABridge\ABridge\Mod\FileBase;
use ABridge\ABridge\UtilsC;

require_once("ModBase_Abst_Case.php");

class ModBase_Abst_Mem_Test extends ModBase_Abst_Case
{

    public static function setUpBeforeClass()
    {
        $classes = ['test1','test2'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['memBase']);
        
        self::$CName= $prm['memBase']['test1'];
        self::$HName= $prm['memBase']['test2'];
        
        self::$DBName=null;
        $fpath=$prm['application']['path'];
        
        self::$db = new FileBase($fpath, self::$DBName);
    }
}
