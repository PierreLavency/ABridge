<?php
namespace ABridge\ABridge\Hdl;

use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\Comp;

class Hdl extends Comp
{
    private static $instance = null;
    protected $isNew=false;
    
    private function __construct()
    {
    	$this->isNew=false;
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Hdl();
        }
        return self::$instance;
    }
    
    public static function reset()
    {
        self::$instance =null;
        return true;
    }
    
    public function init($appPrm, $prm)
    {
        if (isset($prm['Usr'])) {
            Usr::get()->init($appPrm, $prm['Usr']);
        }
    }
    
    public function begin($appPrm, $prm)
    {
        if (isset($prm['Usr'])) {
            $sessionHdl= Usr::get()->begin($appPrm, $prm['Usr']);
            if (Usr::get()->isNew()) {
                $handle = new Handle('/Session/~', CstMode::V_S_UPDT, $sessionHdl);
                $this->isNew=true;
            } else {
                $handle = new Handle($sessionHdl);
            }
        } else {
            $handle = new Handle(null);
        }
        return $handle;
    }
    
    public function isNew()
    {
        return $this->isNew;
    }
    
    public function initMeta($appPrm, $config)
    {
        return true;
    }
}
