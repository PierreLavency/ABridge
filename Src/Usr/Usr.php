<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Handler;

class Usr
{

    public static $cleanUp = false;
    protected static $isNew = false;
    public static $timer= 0; // 0 when connected
    protected $Keep = false;
    
    protected $cookies=[];
    protected $sessions=[];
    protected $changed = false;
    protected $cookieName;
    protected $session;

    public static function init($name, $prm)
    {

        handler::get()->setCmod('Session', 'ABridge\ABridge\Usr\Session');
        handler::get()->setCmod('User', 'ABridge\ABridge\Usr\User');
        handler::get()->setCmod('Role', 'ABridge\ABridge\Usr\Role');
        handler::get()->setCmod('Distribution', 'ABridge\ABridge\Usr\Distribution');
    }
    
    public static function begin($name, $prm)
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
        self::$isNew=false;
        if ($sessionHdl->isNew()) {
            self::$isNew=true;
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
    
    public static function isNew()
    {
        return self::$isNew;
    }
}
