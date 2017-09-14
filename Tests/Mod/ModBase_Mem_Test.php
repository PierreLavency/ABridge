<?php
    
use ABridge\ABridge\Mod\FileBase;
use ABridge\ABridge\UtilsC;

require_once 'ModBase_Case.php';

class ModBase_Mem_Test extends ModBase_Case
{

    public static function setUpBeforeClass()
    {
        $classes = ['test1'];
        $prm=UtilsC::genPrm($classes, get_called_class(), ['memBase']);
        
        self::$CName= $prm['memBase']['test1'];
        self::$DBName= null;
        
        $fpath=$prm['application']['path'];

        self::$db = new FileBase($fpath, null);
    }
}
