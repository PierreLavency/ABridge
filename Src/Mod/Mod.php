<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Handler;
use ABridge\ABridge\Comp;

class Mod extends Comp
{
    protected static $isNew=false;
    protected $mods=[];
    protected $bases=[];
    
    private static $instance = null;
    
    private function __construct()
    {
    }

    public function reset()
    {
        $this->mods=[];
        $this->bases=[];
        self::$instance =null;
        return true;
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Mod();
        }
        return self::$instance;
    }
    
    public function init($prm, $config)
    {
        foreach ($config as $classN => $handler) {
            $c = count($handler);
            switch ($c) {
                case 0:
                    $handler[0]=$prm['base'];
                    // default set
                case 1:
                    if ($handler[0]=='dataBase') {
                        $handler[]=$prm['dbnm'];
                    }
                    if ($handler[0]=='fileBase') {
                        $handler[]=$prm['flnm'];
                    }
                    // default set
                case 2:
                    Handler::get()->setBase($handler[0], $handler[1], $prm);
                    Handler::get()->setStateHandler(
                        $classN,
                        $handler[0],
                        $handler[1]
                    );
                    break;
            }
            $this->mods[]=$classN;
        }
        $this->bases = Handler::get()->getBaseClasses();
    }
    
    public function begin($app, $prm)
    {
        foreach ($this->bases as $base) {
            $base-> beginTrans();
        }
    }
    
    public function isNew()
    {
        return true;
    }
    
    
    public function getMods()
    {
        return $this->mods;
    }
}
