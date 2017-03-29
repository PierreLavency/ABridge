<?php

require_once("ModBase.php");
require_once("FileBase.php");
require_once("SQLBase.php");

function initStateHandler($modName, $base, $instance)
{
    $y = Handler::get()->setStateHandler($modName, $base, $instance);
    return ($y);
}

function getStateHandler($modName)
{
    $y = Handler::get()->getStateHandler($modName);
    return ($y);
}

function getBaseHandler($base, $instance)
{
    $y = Handler::get()->getBase($base, $instance);
    return ($y);
}


function resetHandlers()
{
    $y = Handler::get()->resetHandlers();
    return true;
}
    
class Handler
{
/**
* @var Singleton
* @access private
* @static
*/
    private static $instance = null;
    private $filePath;
    private $bases = []; //'fileBase'=> [name => class],
    private $basesClasses =['fileBase'=>'FileBase','fileSession'=>'FileBase','dataBase'=>'SQLBase'];
    private $modHandler= [];
    private $modBase =['fileBase' =>'ModBase','fileSession' =>'ModBase','dataBase'=>'ModBase'];
    private $viewHandler=[]; // mod => spec
    
/**
* Constructeur de la classe
*
* @param void
* @return void
*/
    private function __construct()
    {
    }
/**
* Méthode qui crée l'unique instance de la classe
* si elle n'existe pas encore puis la retourne.
*
* @param void
* @return Singleton
*/
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Handler();
        }
        return self::$instance;
    }
    
    public function resetHandlers()
    {
        $this->bases= [];
        $this->modHandler=[];
        $this->viewHandler=[];
        self::$instance =null;
    }

    public function getBase($base, $instance)
    {

        return $this->getBaseNm($base, $instance, $instance);
    }

    public function getBaseNm($base, $instance, $name)
    {
        if (! array_key_exists($base, $this->basesClasses)) {
            return false;
        }
        $instances=[];
        if (array_key_exists($base, $this->bases)) {
            $instances=$this->bases[$base];
            if (array_key_exists($instance, $instances)) {
                return $instances[$instance];
            }
        };
        $classN = $this->basesClasses[$base];
        $x = new $classN($name,'cl822','cl822');
        $instances[$instance]=$x;
        $this->bases[$base]=$instances;
        return $x;
    }
    
    
    public function getViewHandler($modName)
    {
        if (isset($this->viewHandler[$modName])) {
            return ($this->viewHandler[$modName]);
        }
        return null;
    }
    
    public function setViewHandler($modName, $spec)
    {
        $this->viewHandler[$modName]=$spec;
        return true;
    }
    
    public function getStateHandler($modName)
    {
        if (array_key_exists($modName, $this->modHandler)) {
            return ($this->modHandler[$modName]);
        }
        return false;
    }

    public function setStateHandler($modName, $base, $instance)
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
}
