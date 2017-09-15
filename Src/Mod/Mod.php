<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Comp;
use ABridge\ABridge\CstError;

use Exception;

class Mod extends Comp
{
    protected static $isNew=false;

    private static $instance = null;
    private $bases = []; //'fileBase'=> [name => class],
    private $basesClasses =[
            'memBase'=>'ABridge\ABridge\Mod\FileBase',
            'fileBase'=>'ABridge\ABridge\Mod\FileBase',
            'dataBase'=>'ABridge\ABridge\Mod\SQLBase',
            
    ];
    private $modHandler= [];
    private $modBase =[
            'memBase' =>'ABridge\ABridge\Mod\ModBase',
            'fileBase' =>'ABridge\ABridge\Mod\ModBase',
            'dataBase'=>'ABridge\ABridge\Mod\ModBase',
            
    ];
    
    private $cmod=[]; //mod=> Cmodclass

    
    private function __construct()
    {
    }

    public function reset()
    {
        $this->bases= [];
        $this->modHandler=[];
        $this->cmod=[];
        $this->comp=[];
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
                    $this->setBase($handler[0], $handler[1], $appPrm);
                    $res = $this->setStateHandler(
                        $classN,
                        $handler[0],
                        $handler[1]
                    );
                    break;
            }
        }
    }
    
    public function begin($appPrm = null, $config = null)
    {
        $bases =$this->getBaseClasses();
        foreach ($bases as $base) {
            $base-> beginTrans();
        }
    }
    
    public function end()
    {
        $res = true;
        $bases =$this->getBaseClasses();
        foreach ($bases as $base) {
            $r =$base->commit();
            $res = ($res and $r);
        }
        return $res;
    }

    public function isNew()
    {
        return true;
    }
    
    public function initMeta($appPrm, $config)
    {
        return true;
    }
        
    
    public function getBase($base, $instance)
    {
        if (array_key_exists($base, $this->bases)) {
            $instances=$this->bases[$base];
            if (array_key_exists($instance, $instances)) {
                return $instances[$instance];
            }
        };
        return null;
    }
    
    public function setBase($base, $instance, $prm)
    {
        if (! array_key_exists($base, $this->basesClasses)) {
            throw new Exception(CstError::E_ERC063.':'.$base);
        }
        $instances=[];
        if (array_key_exists($base, $this->bases)) {
            $instances=$this->bases[$base];
            if (array_key_exists($instance, $instances)) {
                return $instances[$instance];
            }
        };
        $classN = $this->basesClasses[$base];
        $path = $prm['path'];
        switch ($base) {
            case 'memBase':
                $x = new $classN($path,null);
                break;
            case 'fileBase':
                $x = new $classN($path,$instance);
                break;
            case 'dataBase':
                $x = new $classN($path, $prm['host'],$prm['user'],$prm['pass'],$instance);
                break;
            default:
                throw Exception($base);
        }
        $instances[$instance]=$x;
        $this->bases[$base]=$instances;
        return $x;
    }
    
    public function getBaseClasses()
    {
        $res=[];
        foreach ($this->bases as $base => $baseClasses) {
            foreach ($baseClasses as $name => $baseClass) {
                $res[]=$baseClass;
            }
        }
        return $res;
    }

    public function getMods()
    {
        $res= array_keys($this->modHandler);
        return $res;
    }
    
    public function getCmod($modName)
    {
        if (isset($this->cmod[$modName])) {
            return ($this->cmod[$modName]);
        }
        if (class_exists($modName)) {
            return $modName;
        }
        return null;
    }
    
    public function setCmod($modName, $spec)
    {
        $this->cmod[$modName]=$spec;
        return true;
    }
    
    public function getStateHandler($modName)
    {
        if (array_key_exists($modName, $this->modHandler)) {
            return ($this->modHandler[$modName]);
        }
        return false;
    }

    private function setStateHandler($modName, $base, $instance)
    {
        $y= $this-> getStateHandler($modName);
        if ($y) {
            return $y;
        }
        $x = $this->getBase($base, $instance);
        $classN = $this->modBase[$base];
        $y= new $classN($x);
        $this->modHandler[$modName]=$y;
        return $y;
    }




//    
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
            $log = $x->getErrLog();

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

                return false;
            }
        }
        return true;
    }
}
