<?php
namespace ABridge\ABridge;

abstract class Comp
{
    abstract public static function get();

    abstract public static function reset();
    
    abstract public function init($appPrm, $config);
    
    abstract public function begin($appPrm, $config);
    
    abstract public function isNew();
    
    abstract public function initMeta($appPrm, $config);
}
