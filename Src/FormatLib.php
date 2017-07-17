<?php
namespace ABridge\ABridge;

class FormatLib
{
    public static function getTab($level)
    {
        $tab = "";
        if ($level >= 0) {
            for ($i=0; $i<$level; $i++) {
                $tab=$tab."\t";
            }
        }
        return $tab;
    }
    
    public static function getNl($level)
    {
        $nl = "";
        if ($level >= 0) {
            $nl = "\n";
        }
        return $nl;
    }
}
