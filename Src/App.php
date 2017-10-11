<?php
namespace ABridge\ABridge;

abstract class App
{

    abstract public static function init($prm, $config);

    abstract public static function initMeta($config);
    
    abstract public static function initData($config);
}
