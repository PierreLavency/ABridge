<?php
namespace ABridge\ABridge;

abstract class Comp
{
    abstract public static function get();

    abstract public function reset();
    
    abstract public function init($prm, $config);
    
    abstract public function begin($app, $prm);
    
    abstract public function isNew();
}
