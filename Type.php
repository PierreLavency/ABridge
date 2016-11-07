<?php

	require_once("TypeConstant.php");

	
	function isMtype ($x) {
		$l=[M_INT,M_INTP,M_FLOAT,M_BOOL,M_STRING,M_ID,M_REF,M_CREF,M_CODE,M_TMSTP,M_DATE, M_ALPHA,M_ALNUM, ];
		return (in_array($x,$l));
	}
	
	
	function baseType($type) {
		if ($type==M_ID or $type == M_REF or $type == M_CREF or $type==M_CODE) {return (M_INTP) ;}
		return ($type);
	}
	
	
	function convertString($X,$Type) {
		$type = baseType($Type);
		if ($type== M_INTP) {$type=M_INT;}
		if (is_string($X)) {
			switch($type) {
				case M_INT:
					if(ctype_digit($X)) {$X = (int) $X; return $X;};
					break; 
				case M_FLOAT:
					if(is_numeric($X)) {$X = (float) $X;return $X;};
					break;
				case M_BOOL:
					if($X == "false" ) {$X = false;return $X;};
					if($X == "true"  ) {$X = true;return $X;};
					break;
				default: return $X;
			}
		};
		return $X;
	}

	function convertTime($X) {
		if (($timestamp=strtotime($X))==false) {return false;}
		$t = date(M_FORMAT_T,$timestamp);
		return $t;
	}
	function convertDate($X) {
		$d=DateTime::createFromFormat(M_FORMAT_D,$X);
		$t = date(M_FORMAT_D,$timestamp);
		return $t;
	}
	
	function checkType ($X,$type) {
		switch($type) {
			case M_DATE:
				$d=DateTime::createFromFormat(M_FORMAT_D,$X);
				return ($d && $d->format(M_FORMAT_D)==$X);
			case M_TMSTP:
				$d=DateTime::createFromFormat(M_FORMAT_T,$X);
				return ($d && $d->format(M_FORMAT_T)==$X);
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
			case M_ALNUM:
				if (is_string($X)) {return ctype_alnum($X);}
				return 0;
				break;
			case M_ALPHA:
				if (is_string($X)) {return ctype_alpha($X);}
				return 0;
				break;
			case M_INTP:
				if (is_int($X)) {return ($X>=0);}
				return 0;
				break;
			default:
				return 0;
		}
	}

	function convertSqlType($Type) {
		$type = baseType($Type);
		switch($type) {
			case M_DATE:
				return 'DATE';
			case M_TMSTP:
				return 'TIMESTAMP';
			case M_INT:
				return 'INT(11)';
			case M_FLOAT:
				return 'FLOAT';
			case M_BOOL:
				return 'BOOLEAN';
			case M_STRING:
				return 'VARCHAR(255)';
			case M_ALNUM:
				return 'VARCHAR(255)';
			case M_ALPHA:
				return 'VARCHAR(255)';
			case M_INTP:
				return 'INT(11) UNSIGNED';
			default:
				return 0;
		}
	}
	
?>	