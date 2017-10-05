<?php
namespace ABridge\ABridge;

abstract class App
{
    public static $config;
    
    public static function init($prm, $config)
    {
        return self::$config;
    }
    
    abstract public static function initMeta($config);
    
    abstract public static function initData($config);
}
