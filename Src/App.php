<?php
namespace ABridge\ABridge;

abstract class App
{
    public static $config;
    
    abstract public static function loadMeta($prm);
    
    abstract public static function loadData($prm);
}
