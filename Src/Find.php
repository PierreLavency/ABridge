<?php

class Find
{
 //   private function __construct() {}
    
    public static function existsObj($mod)
    {
        $res = self::allId($mod);
        return (! $res == []);
    }
    
    public static function allId($mod)
    {
        $obj = new Model($mod);
        $obj->setCriteria([], [], []);
        $res = $obj->select();
        return $res;
    }
    
    public static function byKey($mod, $bkey, $val)
    {
        $obj = new Model($mod);
        $obj= $obj->getBkey($bkey, $val);
        return ($obj);
    }
}
