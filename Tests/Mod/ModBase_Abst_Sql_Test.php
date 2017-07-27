<?php

use ABridge\ABridge\Mod\SQLBase;

require_once("ModBase_Abst_Case.php");

class ModBase_Abst_Sql_Test extends ModBase_Abst_Case
{
    public static function setUpBeforeClass()
    {
            self::$CName=get_called_class().'_1';
            self::$HName=get_called_class().'_2';
            self::$DBName= 'test';
            $prm=[
            		'path'=>'C:/Users/pierr/ABridge/Datastore/',
            		'host'=>'localhost',
            		'user'=>'cl822',
            		'pass'=>'cl822'
            ];
            self::$db = new SQLBase(
            		$prm['path'],
            		$prm['host'],
            		$prm['user'],
            		$prm['pass'],
            		self::$DBName);
    }
}
