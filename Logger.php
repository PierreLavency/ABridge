
<?php

class Logger{

	public $lines = [];
	public $filePath ='C:\Users\pierr\ABridge\Datastore\\';
	public $fileName;
	public $name;
	
	function  __construct($name= "defaultLoggerFileName" ) {
		$this->name = $name;
		$this->fileName = $this->filePath.$name.".txt";
	}
	public function logLine ($line){
		$this->lines[] = $line;
		$r = count($this->lines);
		$r--;
		return $r;
	}
	public function show () {
		echo "log content of $this->name";
		echo "<br>";
		for ($i=0;$i<count($this->lines);$i++) {
			echo $this->lines[$i];
			echo "<br>";
		}
		echo "<br>";
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
		if (! ($c == $log->logSize())) {return $c+1;}
		for ($i=0;$i<$c;$i++) {
			if (! ($this->lines[$i]==$log->getLine($i))){return $i;}
		}
		return 0;
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