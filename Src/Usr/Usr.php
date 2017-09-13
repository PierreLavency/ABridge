<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Handler;
use ABridge\ABridge\Mod\Mod;

use ABridge\ABridge\Comp;

class Usr extends Comp
{
    const USER ='User';
    const ROLE = 'Role';
    const SESSION ='Session';
    const DISTRIBUTION = 'Distribution';
    const USERGROUP ='UserGroup';
    const GROUPUSER ='GroupUser';
    
    const DEFAUTCLASSNAME = __NAMESPACE__.'\\'.self::SESSION;
    
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

    public function init($appPrm, $bindings)
    {
        $bindings = self::normBindings($bindings);
        foreach ($bindings as $mod => $physicalName) {
            if ($mod == $physicalName) {
                $className= __NAMESPACE__.'\\'.$mod;
                handler::get()->setCmod($mod, $className);
            }
        }
        Mod::get()->init($appPrm, self::defltHandlers($bindings));
    }
    
    public function begin($appPrm, $bindings)
    {
        $className = self::DEFAUTCLASSNAME;
        $cookieName = self::SESSION;
        if (isset($bindings[self::SESSION])) {
            $sessionBinding= $bindings[self::SESSION];
            if ($sessionBinding != self::SESSION) {
                $className=$sessionBinding;
                $cookieName=$sessionBinding;
            }
        }
        $name = $appPrm['name'].$cookieName;
        $id=0;
        if (self::$cleanUp) {
            if (isset($_COOKIE[$name])) {
                unset($_COOKIE[$name]);
            }
        }
        if (isset($_COOKIE[$name])) {
            $id=$_COOKIE[$name];
        }
        $this->isNew=false;
        $sessionHdl = $className::getSession($id, ['Name'=>$name]);
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
    
    
    public function initMeta($appPrm, $bindings)
    {
        return Mod::get()->initModBindings($bindings);
    }
}
