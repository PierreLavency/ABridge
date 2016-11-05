<?php

	require_once("Type.php");
	
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