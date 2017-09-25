<?php
namespace ABridge\ABridge\Log;

use ABridge\ABridge\Log\Logger;

use ABridge\ABridge\Comp;

//use ABridge\ABridge\Handler;

class Log extends Comp
{
    const ADMIN='Admin';
    
    private static $instance = null;
    private $logger;
    private $logLevel=0;
    private $runLevel=0;
    private $path;
    
    private function __construct()
    {
        $this->logger=null;
        $this->runLevel=0;
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Log();
        }
        return self::$instance;
    }
    
    public static function reset()
    {
        self::$instance =null;
        return true;
    }
    
    
    public function init($appPrm, $config)
    {
        $this->logLevel=$appPrm['trace'];
        $this->path=$appPrm['path'];
        return true;
    }
    
    public function begin($appPrm = null, $logLevel = null)
    {
        $this->runLevel=$this->logLevel;
    }
    
    public function logLine($line, $attributes)
    {
        if ($this->runLevel == 0) {
            return true;
        }
        if (! $this->logger) {
            $this->logger=new Logger();
        }
        $lineNumber= $this->logger->logLine($line, $attributes);
        if ($this->runLevel == 1) {
            $this->logger->showLine($lineNumber, true);
        }
    }
    
    public function getLastLine()
    {
        $lastLine = null;
        if ($this->logger) {
            $lineNumber = $this->logger->logSize();
            if ($lineNumber) {
                $lastLine = $this->logger->getLine($lineNumber-1);
            }
        }
        return $lastLine;
    }
   
    public function end($appPrm = null, $bindings = null)
    {
        if ($this->runLevel == 2) {
            $this->logger->show();
        }
        
        $this->runLevel=0;
    }
    
    
    public function isNew()
    {
        return false;
    }
    
    public function initMeta($appPrm = null, $bindings = null)
    {
        return true;
    }
}
