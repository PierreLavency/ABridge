
<?php

class Logger{

	public $lines = [];

	
	public function logLine ($line){
		$this->lines[] = $line;
		$r = count($this->lines);
		$r--;
		return $r;
	}
	
	public function show () {
		for ($i=0;$i<count($this->lines);$i++) {
			echo $this->lines[$i];
			echo "<br>";
		}
	}
}
?>