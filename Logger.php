
<?php

class Logger{

	public $lines = [];
	public $filePath ='C:\Users\pierr\ABridge\Logstore\\';
	public $fileName;
	public $name;
	
	function  __construct($name= "defaultLoggerFileName" ) {
		$this->name = $name;
		$this->fileName = $this->filePath.$name.".txt";
	}
	public function logLine ($line){
		$this->lines[] = $line;
		$r = count($this->lines);
		return $r;
	}
	
	public function show ($show=true) {
		$c = count($this->lines);
		if (!$c) {
			$result = ""; 
			if ($show) {echo $result;}; 
			return $result;
			}
		$result="<br>";
		for ($i=0;$i<$c;$i++) {
			$result=$result. "LINE:".$i."<br>";
			$result=$result. $this->lines[$i];
			$result=$result."<br>";
			}
		$result=$result."<br>";
		if ($show) {echo $result;}
		return $result;
	}

	public function showLine($i){
		if ($i < count($this->lines)) {
			echo "LINE:".$j."<br>";
			echo $this->lines[$i];
			echo "<br>";
			return $this->lines[$i];
		}
		return 0;
	}

	public function getLine($i) {
		if ($i < count($this->lines)) {
			return $this->lines[$i];
		}
		return 0;
	}

	public function logSize() {
		return count($this->lines);
	}
	public function diff ($log){
		$c = count($this->lines);
		if (! ($c == $log->logSize())) {return -1;}
		for ($i=0;$i<$c;$i++) {
			if (! ($this->lines[$i]==$log->getLine($i))){$j=$i+1;return $j;}
		}
		return 0;
	}
	public function includeLog ($log){
		$c = $log->logSize();
		for ($i=0;$i<$c;$i++) {
			if (! $this->logLine($log->getLine($i))) {return 0;} ; 
		}
		$r = $c +1;
		return $r;
	}
	public function save() {
		$file = serialize($this->lines);
        $r=file_put_contents($this->fileName,$file,FILE_USE_INCLUDE_PATH);
		return $r;
	}
	public function load() {
        $file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
		$this->lines = unserialize($file);
		return $file;
	}
	

}
?>