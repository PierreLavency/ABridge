<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Log\Log;

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
        $obj->setCriteria([], [], [], []);
        $res = $obj->select();
        return $res;
    }
    
    public static function byKey($mod, $bkey, $val)
    {
        Log::get()->logLine(
            "mod : $mod bkey: $bkey val: $val",
            [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__]
        );
        $obj = new Model($mod);
        $obj= $obj->getBkey($bkey, $val);
        return ($obj);
    }
}
