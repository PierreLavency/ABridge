<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\Comp;
use ABridge\ABridge\CstError;
use Exception;

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
    public static $cookieTimer= 0; // 0 when connected
    public static $sessionTimer = 6000;
    private static $instance = null;
    
    protected $bindings;
    protected $appPrm;
    protected $isNew = false;
    protected $isInit=false;
    
    private function __construct()
    {
        $this->isNew=false;
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Usr();
        }
        return self::$instance;
    }

    public static function reset()
    {
        self::$instance =null;
        return true;
    }

    public function init($appPrm, $bindings)
    {
        if ($this->isInit) {
            throw new Exception(CstError::E_ERC068.':Usr');
        }
        $bindings = ModUtils::normBindings($bindings);
        $this->bindings=$bindings;
        $this->appPrm=$appPrm;
        foreach ($bindings as $mod => $physicalName) {
            if ($mod == $physicalName) {
                $className= __NAMESPACE__.'\\'.$mod;
                Mod::get()->assocClassMod($mod, $className);
            }
        }
        Mod::get()->init($appPrm, ModUtils::defltHandlers($bindings));
        $this->isInit=true;
    }
    
    public function begin($prm = null)
    {
        if (! $this->isInit) {
            throw new Exception(CstError::E_ERC067.':Usr');
        }
        
        $bindings = $this->bindings;
        $appPrm = $this->appPrm;
        
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
        $key=0;
        if (self::$cleanUp) {
            if (isset($_COOKIE[$name])) {
                unset($_COOKIE[$name]);
            }
        }
        if (isset($_COOKIE[$name])) {
            $key=$_COOKIE[$name];
        }
        $this->isNew=false;
        $sessionHdl = $className::getSession($key, ['Name'=>$name], self::$sessionTimer);
        if ($sessionHdl->isNew()) {
            $this->isNew=true;
            $key = $sessionHdl->getKey();
            $end = 0;
            if (self::$cookieTimer) {
                $end = time() + self::$cookieTimer;
            }
            if (php_sapi_name()==='cli') {
                $_COOKIE[$name]=$key;
            } else {
                setcookie($name, $key, $end, "/");
            }
        }
        return $sessionHdl;
    }
    
    public function isNew()
    {
        return $this->isNew;
    }
    
    public function initMeta()
    {
        if (! $this->isInit) {
            throw new Exception(CstError::E_ERC067.':Usr');
        }
        $bindings=$this->bindings;
        $res= ModUtils::initModBindings($bindings);
        return $bindings;
    }
}
