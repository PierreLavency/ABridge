
<?php
	require_once("Logger.php");

	class unitTest {
		public $logName;
		public $runLogger;
		public $testLogger;
		public $verbatim=1;
		public $init;
	
		function  __construct($Name, $init=0) {
			$this->logName = $Name;
			$this->init = $init;
			if ($init) {
				$this->runLogger = new Logger($this->logName); 
				$this->testLogger = $this->runLogger;
				$this->verbatim = 2;
			}
			else {
				$this->runLogger = new Logger($this->logName."_run"); 
				$this->testLogger= new Logger($this->logName);
			}
		}
	
		function logLine ($line){
			return $this->runLogger->logLine ($line);
		}
		function setVerbatim ($level) {
			$this->verbatim = $level;
			return $level;
		}
		
		function save() {
			$result = "Test : " . $this->logName . " on : " . date("d-m-Y H:i:s") . " result :";
			if ($this->init){
				$r = $this->testLogger->save();
				if ($r) {
					$result = $result . " sucessfully initialized";
				}
				else {
					$result = $result . " initialisation failed ";
				}
			}
			else {
				$this->testLogger->load();
				$r = $this->testLogger->diff($this->runLogger);
				if ($r) {
						$result= $result . "ko diff in line $r";
						$this->runLogger->save();
				}
				else {$result = $result . "ok";};
			}
			
			if ($this->verbatim > 0){
				echo $result. "<br><br>";
			}
			if ($this->verbatim > 1){
				echo "ACTUAL"."<br>";
				$this->runLogger->show();
				if (! $this->init) {
					echo "EXPECTED"."<br>";
					$this->testLogger->show();
					
				}
			}
		}
	
	}
?>
