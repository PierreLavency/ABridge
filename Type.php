<?php

	define('M_INT',"m_int");
	define('M_FLOAT',"m_float");
	define('M_BOOL',"m_bool");
	define('M_STRING',"m_string");

	function convertString($X,$type) {
		if (is_string($X)) {
			switch($type) {
				case M_INT:
					if(ctype_digit($X)) {$X = (int) $X;return $X;};
					break; 
				case M_FLOAT:
					if(is_numeric($X)) {$X = (float) $X;return $X;};
					break;
				case M_BOOL:
					if($X == "false" ) {$X = false;return $X;};
					if($X == "true"  ) {$X = true;return $X;};
					break;
				default: return 0;
			}
		};
		return 0;
	}

	function checkType ($X,$type) {
		switch($type) {
			case M_INT:
				return is_int($X);
				break; 
			case M_FLOAT:
				return is_float($X);
				break;
			case M_BOOL:
				return is_bool($X);
				break; 
			case M_STRING:
				return is_string($X);
				break; 
			default:
				return 0;
		}
	}
	
	
?>	