<?php
namespace ABridge\ABridge;

/**
 * Logger  class file
 *
 * PHP Version 5.6
 *
 * @category PHP
 * @package  ABridge
 * @author   Pierre Lavency <pierrelavency@hotmail.com>
 * @link     No
 */
 
 /**
 * Logger class
 *
 * Loggers are used in many classes to log errors or trace executions
 *
 * @category PHP
 * @package  ABridge
 * @author   Pierre Lavency <pierrelavency@hotmail.com>
 * @link     No
 */
class Logger
{
    public $lines = [];
    protected static $filePath ="";
    public $fileName;
    public $name;

    /**
     * Constructor
     *
     * @param string $name file name to be used if saved.
     */
    public function __construct($name = "defaultLoggerFileName")
    {
        $this->name = $name;
        $this->fileName = self::$filePath.'Logstore/'.$name.".txt";
    }

    public static function setPath($path)
    {
        self::$filePath =$path;
        return true;
    }
    
    public static function getPath()
    {
        return self::$filePath;
    }

    public static function exists($id)
    {
        $f = self::$filePath.'Logstore/'. $id.'.txt';
        return file_exists($f);
    }
    
    /**
     * Log a line
     *
     * @param string $line to be logged.
     *
     * @return int line number
     */
    public function logLine($line)
    {
        $r = count($this->lines);
        $this->lines[] = $line;
        return $r;
    }

    /**
     * Show logged lines
     *
     * @param boolean $show echo or not the output.
     *
     * @return string output string
     */
    public function show($show = true)
    {
        $c = count($this->lines);
        if (!$c) {
            $result = "";
            if ($show) {
                echo $result;
            }
            return $result;
        }
        $result="<br>";
        for ($i=0; $i<$c; $i++) {
            $result=$result. "LINE:".$i."<br>";
            $result=$result. $this->lines[$i];
            $result=$result."<br>";
        }
        $result=$result."<br>";
        if ($show) {
            echo $result;
        }
        return $result;
    }

    /**
     * Show a logged line
     *
     * @param int $i line to be shown.
     *
     * @return string output string
     */
    public function showLine($i, $show = true)
    {
        $res="";
        if ($i < count($this->lines)) {
            $res= "LINE:".$i."<br>".$this->lines[$i]."<br>";
            if ($show) {
                echo $res;
            }
            return $res;
        }
        return false;
    }
 
    /**
     * Get a logged line
     *
     * @param int $i the line number.
     *
     * @return string the line
     */
    public function getLine($i)
    {
        if ($i < count($this->lines)) {
            return $this->lines[$i];
        }
        return false;
    }

    /**
     * Get number of lines logged
     *
     * @return int the number of lines
     */
    public function logSize()
    {
        return count($this->lines);
    }
 
    /**
     * Compare with another Logger
     *
     * @param Logger $log the logger to compare with
     *
     * @return false if both are the same
     */
    public function diff($log)
    {
        $c = count($this->lines);
        if (! ($c == $log->logSize())) {
            return -1;
        }
        for ($i=0; $i<$c; $i++) {
            if (! ($this->lines[$i]==$log->getLine($i))) {
                $j=$i+1;
                return $j;
            }
        }
        return false;
    }

    /**
     * Include another Logger content
     *
     * @param Logger $log the logger to include
     *
     * @return int the number of lines included
     */
    public function includeLog($log)
    {
        $c = $log->logSize();
        for ($i=0; $i<$c; $i++) {
            $this->logLine($log->getLine($i));
        }
        $r = $c +1;
        return $r;
    }

    /**
     * Save content on file
     *
     * @return int the number of byte saved
     */
    public function save()
    {
        $file = serialize($this->lines);
        $r=file_put_contents($this->fileName, $file, FILE_USE_INCLUDE_PATH);
        return $r;
    }
     /**
     * Load content from file
     *
     * @return int the number of byte saved
     */
    public function load()
    {
        $file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
        $this->lines = unserialize($file);
        return true;
    }
}
