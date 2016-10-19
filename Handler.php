<?php
	require_once("PersistFile.php"); 
	
	
	function getHandler ($X) {
		$y = Handler::getInstance();
		if ($X =="file_persist") {
			return $y-> getFileBase();
		};
		return 0;
	}
	
	class Handler {
  /**
   * @var Singleton
   * @access private
   * @static
   */
		private static $_instance = null;
		private $fileBase = null;
		private $runBase = null;
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
		public function getFileBase() {
			if(is_null($this->fileBase)) {
				$this->fileBase= new fileBase();  
			}
			return $this->fileBase;
		}

		public function runBase() {
			if(is_null($this->runBase)) {
				$this->runBase= new fileBase();  
			}
			return $this->runBase;
		}
	   
	};

?>