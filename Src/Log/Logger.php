<?php
namespace ABridge\ABridge\Log;

class Logger
{
    public $lines = [];
    public $linesAttributes=[];
    protected static $filePath ="";
    public $fileName;
    public $name;
    protected $br;


    public function __construct()
    {
        $this->br="<br>";
        if (php_sapi_name()==='cli') {
            $this->br="\n";
        }
    }
    
    /**
     * Log a line
     *
     * @param string $line to be logged.
     *
     * @return int line number
     */
    public function logLine($line, $attributes = [])
    {
        $r = count($this->lines);
        $this->lines[] = $line;
        $this->linesAttributes[]=$attributes;
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
        $result=$this->br;
        for ($i=0; $i<$c; $i++) {
            $result=$result. $this->showLineHeader($i);
            $result=$result. $this->lines[$i];
            $result=$result.$this->br;
        }
        $result=$result.$this->br;
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
            $res= $this->showLineHeader($i).$this->lines[$i].$this->br;
            if ($show) {
                echo $res;
            }
            return $res;
        }
        return false;
    }
 
    
    protected function showLineHeader($i)
    {
        $header =  "LINE:".$i;
        $attributes = $this->linesAttributes[$i];
        foreach ($attributes as $name => $value) {
            $header = $header.' '.$name.' : '.$value;
        }
        $header=$header.$this->br;
        return $header;
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

    public function getAttributes($i)
    {
        if ($i < count($this->linesAttributes)) {
            return $this->linesAttributes[$i];
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
            $this->logLine($log->getLine($i), $log->getAttributes($i));
        }
        $r = $c +1;
        return $r;
    }

    /**
     * Save content on file
     *
     * @return int the number of byte saved
     */
    public function save($path, $name)
    {
        $this->fileName = $path.'Logstore/'.$name.".txt";
        $fileContent = [$this->lines,$this->linesAttributes];
        $file = serialize($fileContent);
        $r=file_put_contents($this->fileName, $file, FILE_USE_INCLUDE_PATH);
        return $r;
    }
     /**
     * Load content from file
     *
     * @return int the number of byte saved
     */
    public function load($path, $name)
    {
        $this->fileName = $path.'Logstore/'.$name.".txt";
        $file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
        $fileContent= unserialize($file);
        $this->lines=$fileContent[0];
        $this->linesAttributes=$fileContent[1];
        return true;
    }
}
