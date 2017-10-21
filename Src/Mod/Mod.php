<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Comp;
use ABridge\ABridge\CstError;
use Exception;

class Mod extends Comp
{
    protected static $isNew=false;

    private static $instance = null;
    private $baseTypeInstances = []; //'fileBase'=> [name => baseObj],
    private $handlerList= []; // Mod => baseObj
    private $handlerDetailList=[];
    private $baseTypeClasses =[
            'memBase'=>'ABridge\ABridge\Mod\FileBase',
            'fileBase'=>'ABridge\ABridge\Mod\FileBase',
            'dataBase'=>'ABridge\ABridge\Mod\SQLBase',
            
    ];
    private $modBaseClasses =[
            'memBase' =>'ABridge\ABridge\Mod\ModBase',
            'fileBase' =>'ABridge\ABridge\Mod\ModBase',
            'dataBase'=>'ABridge\ABridge\Mod\ModBase',
            
    ];
    
    private $cmod=[]; //mod=> Cmodclass

    
    private function __construct()
    {
        $this->baseTypeInstances= [];
        $this->handlerList=[];
        $this->cmod=[];
        $this->comp=[];
    }

    public static function reset()
    {
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
    
    public function init($appPrm, $configHandlerSpecs)
    {
        foreach ($configHandlerSpecs as $className => $handlerSpec) {
            $c = count($handlerSpec);
            switch ($c) {
                case 0:
                    $handlerSpec[0]=$appPrm['base'];
                    // default set
                case 1:
                    if ($handlerSpec[0]=='dataBase') {
                        $handlerSpec[]=$appPrm['dataBase'];
                    }
                    if ($handlerSpec[0]=='fileBase') {
                        $handlerSpec[]=$appPrm['fileBase'];
                    }
                    if ($handlerSpec[0]=='memBase') {
                        $handlerSpec[]=$appPrm['memBase'];
                    }
                    // default set
                case 2:
                    $base = $handlerSpec[0];
                    $name = $handlerSpec[1];
                    $this->setBase($base, $name, $appPrm);
                    $this->setStateHandler($className, $base, $name);
                    break;
            }
        }
        return true;
    }
    
    public function begin($prm = null)
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
    
    public function close()
    {
        $res = true;
        $bases =$this->getBaseClasses();
        foreach ($bases as $base) {
            $r =$base->close();
            $res = ($res and $r);
        }
        return $res;
    }
    
    public function rollback()
    {
        $res = true;
        $bases =$this->getBaseClasses();
        foreach ($bases as $base) {
            $r =$base->rollback();
            $res = ($res and $r);
        }
        return $res;
    }
    
    
    public function isNew()
    {
        return true;
    }
    
    public function initMeta()
    {
        return [];
    }
           
    public function getBase($baseType, $baseName)
    {
        if (array_key_exists($baseType, $this->baseTypeInstances)) {
            $instances=$this->baseTypeInstances[$baseType];
            if (array_key_exists($baseName, $instances)) {
                return $instances[$baseName];
            }
        };
        return null;
    }
    
    public function setBase($baseType, $baseName, $prm)
    {
        if (! array_key_exists($baseType, $this->baseTypeClasses)) {
            throw new Exception(CstError::E_ERC063.':'.$baseType);
        }
        $instances=[];
        if (array_key_exists($baseType, $this->baseTypeInstances)) {
            $instances=$this->baseTypeInstances[$baseType];
            if (array_key_exists($baseName, $instances)) {
                return $instances[$baseName];
            }
        };
        $className = $this->baseTypeClasses[$baseType];
        $path = $prm['path'];
        switch ($baseType) {
            case 'memBase':
                $baseObj = new $className($path, null);
                break;
            case 'fileBase':
                $baseObj = new $className($path, $baseName);
                break;
            case 'dataBase':
                $baseObj = new $className($path, $prm['host'], $prm['user'], $prm['pass'], $baseName);
                break;
        }
        $instances[$baseName]=$baseObj;
        $this->baseTypeInstances[$baseType]=$instances;
        return $baseObj;
    }
    
    private function setStateHandler($modName, $baseType, $baseName)
    {
        $stateHandler= $this-> getStateHandler($modName);
        if ($stateHandler) {
            return $stateHandler;
        }
        $baseObj = $this->getBase($baseType, $baseName);
        $className = $this->modBaseClasses[$baseType];
        $stateHandler= new $className($baseObj);
        $this->handlerList[$modName]=$stateHandler;
        $this->handlerDetailList[$modName]=[$baseType,$baseName];
        return $stateHandler;
    }
    
    public function getStateHandler($modName)
    {
        if (array_key_exists($modName, $this->handlerList)) {
            return ($this->handlerList[$modName]);
        }
        return false;
    }
    
    public function getBaseClasses()
    {
        $res=[];
        foreach ($this->baseTypeInstances as $base => $baseClasses) {
            foreach ($baseClasses as $name => $baseClass) {
                $res[]=$baseClass;
            }
        }
        return $res;
    }

    public function getMods()
    {
        $res= array_keys($this->handlerList);
        return $res;
    }
    
    public function getClassMod($modName)
    {
        if (isset($this->cmod[$modName])) {
            return ($this->cmod[$modName]);
        }
        if (class_exists($modName)) {
            return $modName;
        }
        return null;
    }
    
    public function assocClassMod($modName, $className)
    {
        $this->cmod[$modName]=$className;
        return true;
    }

    public function showState()
    {
        $showState= [];
        foreach ($this->baseTypeInstances as $baseType => $instances) {
            $showStateInstance=[];
            foreach ($instances as $instanceName => $baseObj) {
                $classList=[];
                foreach ($this->handlerDetailList as $modName => $handlerDetail) {
                    if ($handlerDetail[0]==$baseType and $handlerDetail[1]==$instanceName) {
                        $classList[]=$modName;
                    }
                }
                $showStateInstance[] = [$instanceName,$classList];
            }
            $showState[$baseType]=$showStateInstance;
        }
        return $showState;
    }
}
