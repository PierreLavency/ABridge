<?php

require_once("SQLBase.php");
require_once("ModBase_Abst_Case.php");

class ModBase_Abst_Sql_Test extends ModBase_Abst_Case
{
    public static function setUpBeforeClass()
    {
            self::$CName=get_called_class().'_1';
            self::$HName=get_called_class().'_2';
            self::$DBName= 'test';
            self::$db = new SQLBase(self::$DBName);
    }
}
