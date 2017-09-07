<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Handler;
use ABridge\ABridge\Comp;

class Usr extends Comp
{

    public static $cleanUp = false;
    public static $timer= 0; // 0 when connected


    private static $instance = null;
    protected $isNew = false;
    
    private function __construct()
    {
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Usr();
        }
        return self::$instance;
    }

    public function reset()
    {
        $this->isNew=false;
        self::$instance =null;
        return true;
    }

    public function init($name, $config)
    {

        foreach ($config as $mod => $cname) {
            if (is_numeric($mod)) {
                $mod = $cname;
                $cname = __NAMESPACE__.'\\'.$mod;
            }
            handler::get()->setCmod($mod, $cname);
        }
    }
    
    public function begin($name, $prm)
    {
        $className=$prm[0];
        $id=0;
        
        if (self::$cleanUp) {
            if (isset($_COOKIE[$name])) {
                unset($_COOKIE[$name]);
            }
        }
        if (isset($_COOKIE[$name])) {
            $id=$_COOKIE[$name];
        }
        
        $sessionHdl = $className::getSession($id);
        if ($sessionHdl->isNew()) {
            $this->isNew=true;
            $id = $sessionHdl->getKey();
            $end = 0;
            if (self::$timer) {
                $end = time() + self::$timer;
            }
            if (php_sapi_name()==='cli') {
                $_COOKIE[$name]=$id;
            } else {
                setcookie($name, $id, $end, "/");
            }
        }
        return $sessionHdl;
    }
    
    public function isNew()
    {
        return $this->isNew;
    }
}
