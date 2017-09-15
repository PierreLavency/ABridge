<?php
namespace ABridge\ABridge\Adm;

use ABridge\ABridge\Comp;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;

use ABridge\ABridge\Handler;

class Adm extends Comp
{
    const ADMIN='Admin';
    
    protected $isNew = false; // not reentrent
    private static $instance = null;
    
    private function __construct()
    {
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Adm();
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
        if ($bindings==[]) {
            $bindings[self::ADMIN]=self::ADMIN;
        }
        $bindings = self::normBindings($bindings);
        Mod::get()->init($appPrm, self::defltHandlers($bindings));
        if ($bindings[self::ADMIN]==self::ADMIN) {
            $className = __NAMESPACE__.'\\'.self::ADMIN;
            Handler::get()->setCmod(self::ADMIN, $className);
        }
    }
    
    public function begin($appPrm, $bindings)
    {
        $this->isNew = false;
        $mod =self::ADMIN;
        if (isset($bindings[self::ADMIN])) {
            $mod=$bindings[self::ADMIN];
        }
        $obj = new Model($mod);
        $obj->setCriteria([], [], []);
        $res = $obj->select();
        if (count($res)==0) {
            foreach ($appPrm as $attr => $val) {
                if ($attr == 'dataBase') {
                    $attr='dBase';
                }
                $obj->setVal($attr, $val);
            }
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
    
    public function initMeta($appPrm, $bindings)
    {
        if ($bindings==[]) {
            $bindings[self::ADMIN]=self::ADMIN;
        }
        $bindings = self::normBindings($bindings);
        foreach ($bindings as $logicalName => $physicalName) {
            $x = new Model($physicalName);
            $x->deleteMod();
        }
    }
}
