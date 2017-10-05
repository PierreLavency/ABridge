<?php
namespace ABridge\ABridge\Log;

use ABridge\ABridge\Log\Logger;

use ABridge\ABridge\Comp;

//use ABridge\ABridge\Handler;

class Log extends Comp
{
    const TCLASS='class';
    const TFUNCT='function';
    const TLINE='line';
    
    public static $tproperty = [self::TCLASS,self::TFUNCT,self::TLINE];
    private static $instance = null;
    private $logger;
    private $logLevel=0;
    private $runLevel=0;
    private $propList=[];
    private $tdisp=0;

    
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
    
    public function getRunLevel()
    {
        return $this->runLevel;
    }
    
    public function getLevel()
    {
        return $this->logLevel;
    }
    
    public function getDisp()
    {
        return $this->tdisp;
    }
    
    public function init($appPrm, $config)
    {
        $this->logLevel=$appPrm['trace'];
        $this->path=$appPrm['path'];
        $this->propList=[];
        foreach (self::$tproperty as $prop) {
            $tprop='t'.$prop;
            if (isset($appPrm[$tprop])) {
                $this->propList[$prop]=$appPrm[$tprop];
            }
        }
        $this->tdisp=0;
        if (isset($appPrm['tdisp'])) {
            $this->tdisp=(int) $appPrm['tdisp'];
        }
        return true;
    }
    
    public function begin($prm = null)
    {
        $this->runLevel=$this->logLevel;
    }
 
    private function evalcond($attributes)
    {
        foreach ($this->propList as $prop => $val) {
            if ($attributes[$prop] != $val) {
                return false;
            }
        }
        return true;
    }
    
    public function logLine($line, $attributes)
    {
        if ($this->runLevel == 0) {
            return true;
        }
        if (! $this->logger) {
            $this->logger=new Logger();
        }
        if (! $this->evalcond($attributes)) {
            return true;
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
            if ($this->tdisp==0) {
                $this->logger->show();
            }
            if ($this->tdisp==1) {
                $line = $this->logger->logSize().' lines with ';
                foreach ($this->propList as $prop => $val) {
                    $line= $line . $prop. ' : ' . $val;
                }
                echo $line."\n";
            }
        }
        $this->runLevel=0;
    }
    
    
    public function isNew()
    {
        return false;
    }
    
    public function initMeta()
    {
        return [];
    }
}
