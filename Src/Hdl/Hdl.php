<?php
namespace ABridge\ABridge\Hdl;

use ABridge\ABridge\Comp;
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Usr\Usr;

class Hdl extends Comp
{
    private static $instance = null;
    protected $isNew=false;
    protected $prm=null;
    protected $appPrm;
    
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
            $this->prm=$prm['Usr'];
        }
        $this->appPrm=$appPrm;
    }
    
    public function begin($prm = null)
    {
        $this->isNew=false;
        if (isset($this->prm)) {
            $sessionHdl= Usr::get()->begin();
            if (Usr::get()->isNew()) {
                $this->isNew=true;
                return new Handle('/Session/~', CstMode::V_S_UPDT, $sessionHdl);
            }
            return new Handle($sessionHdl);
        }
        return new Handle(null);
    }
    
    public function isNew()
    {
        return $this->isNew;
    }
    
    public function initMeta()
    {
        return [];
    }
}
