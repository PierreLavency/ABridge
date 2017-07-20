<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Handler;

class Mod
{
    protected static $isNew=false;
    
    public static function init($app, $config)
    {
        foreach ($config as $classN => $handler) {
            $c = count($handler);
            switch ($c) {
                case 0:
                    break;
                case 1:
                    $handler[]=$app;
                    // default
                case 2:
                    Handler::get()->setStateHandler($classN, $handler[0], $handler[1]);
                    break;
            }
        }
    }
    
    public static function isNew()
    {
        return self::$isNew;
    }
}
