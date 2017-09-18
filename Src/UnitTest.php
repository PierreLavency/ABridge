<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Log\Logger;

class UnitTest
{
    public $logName;
    private $runName;
    private $testName;
    public $runLogger;
    public $testLogger;
    public $verbatim=0;
    public $init;
    private $path = 'C:/Users/pierr/ABridge/Datastore/';

    public function __construct($path, $name, $init = 0)
    {
        $this->path=$path;
        $this->logName = $name;
        $this->init = $init;
        if ($init) {
            $this->runName=$this->logName;
            $this->testName=$this->logName;
            $this->runLogger = new Logger();
            $this->testLogger = $this->runLogger;
            $this->verbatim = 2;
        } else {
            $this->runName=$this->logName."_run";
            $this->testName=$this->logName;
            $this->runLogger = new Logger();
            $this->testLogger= new Logger();
        }
    }

    public function logLine($line)
    {
        return $this->runLogger->logLine($line);
    }
    
    public function setVerbatim($level)
    {
        $this->verbatim = $level;
        return $level;
    }
    
    public function showTest()
    {
        $this->runLogger->show();
    }
    
    public function includeLog($log)
    {
        return $this->runLogger->includeLog($log);
    }
    
    public function saveTest()
    {
        $err = false;
        $result = "Test : " . $this->logName . " on : ";
        $result=$result . date("d-m-Y H:i:s") . " result :";
        if ($this->init) {
            $r = $this->testLogger->save($this->path, $this->testName);
            if ($r) {
                $result = $result . " sucessfully initialized";
            } else {
                $result = $result . " initialisation failed ";
                $err = true;
            }
        } else {
            $this->testLogger->load($this->path, $this->testName);
            $r = $this->testLogger->diff($this->runLogger);
            if ($r) {
                if ($r>0) {
                    $rline = $r-1;
                    $result= $result . "ko diff in line $rline";
                } else {
                    $result= $result . "ko diff in number of line";
                }
                    $this->runLogger->save($this->path, $this->runName);
                    $err = true;
            } else {
                $result = $result . "ok";
            };
        }
        if ($this->verbatim == 0) {
            if ($err) {
                echo $result. "<br><br>";
            }
        }
        if ($this->verbatim > 0) {
            echo $result. "<br><br>";
        }
        if ($this->verbatim > 1) {
            echo "ACTUAL"."<br>";
            $this->runLogger->show();
            if (! $this->init) {
                echo "EXPECTED"."<br>";
                $this->testLogger->show();
            }
        }
    }
}
