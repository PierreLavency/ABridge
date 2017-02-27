<?php

require_once("ModBase.php"); 
require_once("FileBase.php"); 
require_once("SQLBase.php");

function initStateHandler($modName,$base,$instance) 
{
    $y = Handler::get()->setStateHandler($modName, $base, $instance);
    return ($y);
}

function getStateHandler($modName) 
{
    $y = Handler::get()->getStateHandler($modName);
    return ($y);
}

function getBaseHandler($base,$instance) 
{
    $y = Handler::get()->getBase($base, $instance);
    return ($y);  
}


function resetHandlers() 
{
    $y = Handler::get()-> resetHandlers();
    return true;
}
    
class Handler
{
/**
* @var Singleton
* @access private
* @static
*/
    private static $_instance = null;
    private $_filePath;
    private $_bases = []; //'fileBase'=> [name => class],
    private $_basesClasses =['fileBase'=>'FileBase','dataBase'=>'SQLBase'];
    private $_modHandler= [];
    private $_modBase =['fileBase' =>'ModBase','dataBase'=>'ModBase'];
    private $_viewHandler=[]; // mod => spec
    
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
        if (is_null(self::$_instance)) {
            self::$_instance = new Handler();  
        }
        return self::$_instance;
    }
    
    public function resetHandlers()
    {
        $this->_bases= [];
        $this->_modHandler=[];
        $this->_viewHandler=[];
        self::$_instance =null;
    }

    public function getBase($base,$instance) 
    {

        return $this->getBaseNm($base, $instance, $instance);          
    }

    public function getBaseNm($base,$instance,$name) 
    {
        if (! array_key_exists($base, $this->_basesClasses)) {
            return false;
        }
        $instances=[];
        if (array_key_exists($base, $this->_bases)) {
            $instances=$this->_bases[$base];
            if (array_key_exists($instance, $instances)) {
                return $instances[$instance];
            }
        };
        $classN = $this->_basesClasses[$base];
        $x = new $classN($name,'cl822','cl822');
        $instances[$instance]=$x;
        $this->_bases[$base]=$instances;
        return $x;          
    }
    
    
    public function getViewHandler($modName) 
    {
        if (isset($this->_viewHandler[$modName])) {
            return ($this->_viewHandler[$modName]);
        }
        return null;  
    }
    
    public function setViewHandler($modName,$spec) 
    {
        $this->_viewHandler[$modName]=$spec;
        return true;
    }
    
    public function getStateHandler($modName) 
    {
        if (array_key_exists($modName, $this->_modHandler)) {
            return ($this->_modHandler[$modName]);
        }
        return false;  
    }

    public function setStateHandler($modName,$base,$instance) 
    {
        $y= $this-> getStateHandler($modName);
        if ($y) {
            return $y;
        }
        $x = $this->getBase($base, $instance);
        $classN = $this->_modBase[$base];
        $y= new $classN($x);
        $this->_modHandler[$modName]=$y;    
        return $y;
    }           

};

