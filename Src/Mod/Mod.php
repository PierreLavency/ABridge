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
    private static $handler = null;
    
    private function __construct()
    {
    }

    public function reset()
    {
        Handler::get()->resetHandlers();
        self::$handler=Handler::get();
        $this->mods=[];
        $this->bases=[];
        self::$instance =null;
        return true;
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Mod();
            self::$handler=Handler::get();
        }
        return self::$instance;
    }
    
    public function init($appPrm, $config)
    {
        foreach ($config as $classN => $handler) {
            $c = count($handler);
            switch ($c) {
                case 0:
                    $handler[0]=$appPrm['base'];
                    // default set
                case 1:
                    if ($handler[0]=='dataBase') {
                        $handler[]=$appPrm['dataBase'];
                    }
                    if ($handler[0]=='fileBase') {
                        $handler[]=$appPrm['fileBase'];
                    }
                    if ($handler[0]=='memBase') {
                        $handler[]=$appPrm['memBase'];
                    }
                    // default set
                case 2:
                    self::$handler->setBase($handler[0], $handler[1], $appPrm);
                    self::$handler->setStateHandler(
                        $classN,
                        $handler[0],
                        $handler[1]
                    );
                    break;
            }
            $this->mods[]=$classN;
        }
        $this->bases = self::$handler->getBaseClasses();
    }
    
    
    public function getBase($baseType, $baseName)
    {
        return self::$handler->getBase($baseType, $baseName);
    }
 
    
    public function begin($appPrm = null, $config = null)
    {
        foreach ($this->bases as $base) {
            $base-> beginTrans();
        }
    }

    public function end()
    {
        $res = true;
        foreach ($this->bases as $base) {
            $r =$base->commit();
            $res = ($res and $r);
        }
        return $res;
    }
    
    public function isNew()
    {
        return true;
    }
    
    
    public function getMods()
    {
        return $this->mods;
    }
    
    public function initMeta($appPrm, $config)
    {
        return true;
    }
    
    
    public static function initModBindings($bindings, $logicalNames = null)
    {
        $normBindings=self::normBindings($bindings);
        if (is_null($logicalNames)) {
            $logicalNames = array_keys($normBindings);
        }
        foreach ($logicalNames as $logicalName) {
            $res = self::initModBinding($logicalName, $normBindings);
            if (!$res) {
                return false;
            }
        }
        $res = self::checkMods($logicalNames, $normBindings);
        return $res;
    }
      
    
    public static function initModBinding($logicalName, $normBindings)
    {
        $physicalName=$normBindings[$logicalName];
        $x = new Model($physicalName);
        $x->deleteMod();
        $x->initMod($normBindings);
        $x->saveMod();
        if ($x->isErr()) {
//            echo  $physicalName ;
            $log = $x->getErrLog();
//            $log->show();
            return false;
        }
        return true;
    }
    
    public static function checkMods($logicalNames, $normBindings)
    {
        foreach ($logicalNames as $logicalName) {
            $physicalName=$normBindings[$logicalName];
            $x = new Model($physicalName);
            $res = $x->checkMod();
            if (!$res) {
                $log = $x->getErrLog();
//                $log->show();
                return false;
            }
        }
        return true;
    }
}
