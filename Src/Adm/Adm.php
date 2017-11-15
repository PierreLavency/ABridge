<?php
namespace ABridge\ABridge\Adm;

use ABridge\ABridge\Comp;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\ModUtils;
use Exception;

class Adm extends Comp
{
    const ADMIN='Admin';
    
    protected $isNew = false; // not reentrent
    private static $instance = null;
    private $bindings;
    private $appPrm;
    private $isInit = false;

    
    private function __construct()
    {
        $this->isNew=false;
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Adm();
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
            throw new Exception(CstError::E_ERC068.':Adm');
        }
        $this->isInit=true;
        if ($bindings==[]) {
            $bindings[self::ADMIN]=self::ADMIN;
        }
        $bindings = ModUtils::normBindings($bindings);
        $this->bindings=$bindings;
        $this->appPrm=$appPrm;

        Mod::get()->init($appPrm, ModUtils::defltHandlers($bindings));
        if ($bindings[self::ADMIN]==self::ADMIN) {
            $className = __NAMESPACE__.'\\'.self::ADMIN;
            Mod::get()->assocClassMod(self::ADMIN, $className);
        }
    }
    
    public function getConfig()
    {
        return $this->appPrm['config'];
    }
    
    public function begin($prm = null)
    {
        if (! $this->isInit) {
            throw new Exception(CstError::E_ERC067.':Adm');
        }
        $this->isNew = false;
        $mod =self::ADMIN;
        $bindings=$this->bindings;
        $appPrm=$this->appPrm;

        if (isset($bindings[self::ADMIN])) {
            $mod=$bindings[self::ADMIN];
        }
        $obj = new Model($mod);
        $obj->setCriteria([], [], [], []);
        $res = $obj->select();
        if (count($res)==0) {
            $obj->setVal('Name', $appPrm['name']);
            $obj->setVal('Parameters', json_encode($appPrm, JSON_PRETTY_PRINT));
            $obj->save();
            $this->isNew = true;
        } else {
            $obj = new Model($mod, 1);
        }
        return $obj;
    }
    
    public function isNew()
    {
        return $this->isNew;
    }
    
    public function initMeta()
    {
        if (! $this->isInit) {
            throw new Exception(CstError::E_ERC067.':Adm');
        }
        $bindings=$this->bindings;
        $bindings = ModUtils::normBindings($bindings);
        foreach ($bindings as $logicalName => $physicalName) {
            $x = new Model($physicalName);
            $x->deleteMod();
        }
        return $bindings;
    }
    
    public function isInit()
    {
        return $this->isInit;
    }
}
