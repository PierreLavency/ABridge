<?php

require_once("SQLBase.php");
require_once("ModBase_Case.php");

class ModBase_Sql_Test extends ModBase_Case
{
    public static function setUpBeforeClass()
    {
            self::$CName=get_called_class().'_1';
        ;
            self::$DBName= 'test';
            self::$db = new SQLBase(self::$DBName);
    }
}
