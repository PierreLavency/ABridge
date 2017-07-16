<?php
    
use ABridge\ABridge\FileBase;

require_once 'ModBase_Case.php';

class ModBase_Fle_Test extends ModBase_Case
{

    public static function setUpBeforeClass()
    {
        self::$CName=get_called_class().'_f_1';
        self::$DBName= 'atest';
        self::$db = new FileBase(self::$DBName);
    }
}
