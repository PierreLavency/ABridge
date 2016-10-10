<?php


	function convertString($X,$type) {
		if (is_string($X) {
			switch($type) {
				case "m_int":
					if(ctype_digit($X)) {$X = (int) $X;return $X;};
					break; 
				case "m_float":
					if(is_numeric($X)) {$X = (float) $X;return $X;};
					break;
				case "m_bool":
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
			case "m_int":
				return is_int($X);
				break; 
			case "m_float":
				return is_float($X);
				break;
			case "m_bool":
				return is_bool($X);
				break; 
			case "m_string":
				return is_string($X);
				break; 
			default:
				return 0;
		}
	}
	
	
?>	