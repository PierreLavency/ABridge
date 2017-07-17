<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Logger;

class UnitTest
{
    public $logName;
    public $runLogger;
    public $testLogger;
    public $verbatim=0;
    public $init;

    public function __construct($name, $init = 0)
    {
        $this->logName = $name;
        $this->init = $init;
        if ($init) {
            $this->runLogger = new Logger($this->logName);
            $this->testLogger = $this->runLogger;
            $this->verbatim = 2;
        } else {
            $this->runLogger = new Logger($this->logName."_run");
            $this->testLogger= new Logger($this->logName);
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
            $r = $this->testLogger->save();
            if ($r) {
                $result = $result . " sucessfully initialized";
            } else {
                $result = $result . " initialisation failed ";
                $err = true;
            }
        } else {
            $this->testLogger->load();
            $r = $this->testLogger->diff($this->runLogger);
            if ($r) {
                if ($r>0) {
                    $rline = $r-1;
                    $result= $result . "ko diff in line $rline";
                } else {
                    $result= $result . "ko diff in number of line";
                }
                    $this->runLogger->save();
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
