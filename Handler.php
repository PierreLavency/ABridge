<?php
	require_once("FileBase.php"); 
	require_once("ModBase.php"); 
	
	function initStateHandler ($ModName,$Base,$instance='default') {
		$y = Handler::getInstance();
		return ($y->setStateHandler($ModName,$Base,$instance));
	}
	
	function getStateHandler ($ModName) {
		$y = Handler::getInstance();
		return ($y->getStateHandler($ModName));
	}
	
	function getBaseHandler ($Base,$instance='default') {
		$y = Handler::getInstance();
		return ($y->getBase($Base,$instance));	
	}
		
	class Handler {
  /**
   * @var Singleton
   * @access private
   * @static
   */
		private static $_instance = null;
		private $bases = [];
		private $bases_classes =['fileBase'=>'FileBase'];
		private $mod_handler= [];
		private $mod_base =['fileBase' =>'ModBase'];
   /**
    * Constructeur de la classe
    *
    * @param void
    * @return void
    */
		private function __construct() {  
		}
   /**
    * Méthode qui crée l'unique instance de la classe
    * si elle n'existe pas encore puis la retourne.
    *
    * @param void
    * @return Singleton
    */
		public static function getInstance() {
			if(is_null(self::$_instance)) {
				self::$_instance = new Handler();  
			}
			return self::$_instance;
		}
		
		public function getBase($Base,$Instance) {
			if (! array_key_exists($Base,$this->bases_classes)) {return 0;}
			$Instances=[];
			if(array_key_exists($Base,$this->bases)) {
				$Instances=$this->bases[$Base];
				if(array_key_exists($Instance,$Instances)) {
					return $Instances[$Instance];
				}
			};
			$ClassN = $this->bases_classes[$Base];
			if ($Instance=='default') {
				$x = new $ClassN();
			}
			else {
				$x = new $ClassN($Instance);
			}
			$Instances[$Instance]=$x;
			$this->bases[$Base]=$Instances;
			return $x; 			
		}
	   	
		public function getStateHandler($ModName) {
			if(array_key_exists($ModName,$this->mod_handler)) {return ($this->mod_handler[$ModName]);}
			return 0;  
		}

		public function setStateHandler($ModName,$Base,$Instance) {
			$x = $this->getBase($Base,$Instance);
			$ClassN = $this->mod_base[$Base];
			$y= new $ClassN($x);
			$this->mod_handler[$ModName]=$y;	
			return $y;
		}			

	};

?>