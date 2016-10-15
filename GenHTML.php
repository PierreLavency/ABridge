<?php

	define ('NL_O', "\n");
	define ('TAB_O', "\t");
	
	require_once("ViewConstant.php");
	
	
	function genList($dspecL,$show=true){
		$list_s   = '<ul>  '  ;
		$list_e_s = '</ul>  ';
		$element_s   = '<li>  '  ;
		$element_e_s   = '</li>  '  ;

		$result = $list_s ; 
		foreach ($dspecL as $dspec) {
			$result=$result . NL_O . TAB_O. $element_s. NL_O.genFormElem($dspec,false).TAB_O.$element_e_s;
		}
		$result = $result . NL_O. $list_e_s;
		if ($show) {echo $result;};
		return $result;
	}

	function genFormElem($dspec,$show = true)	 {

 		$button_s   = '<input type="submit" value="Submit">';
		$textarea_s = '<textarea ';
		$textarea_e_s = '</textarea>';
		$select_s   = '<select '  ;
		$select_e_s = '</select> ';
		$input_s    = '<input '   ;
		$option_s   = '<option '  ;
		$option_e_s = '</option> ';
		$end_s      = ' > '	  ;
		$col_s	    = ' cols="'    ;
		$row_s	    = ' rows="'    ;
		

		$type="";
		$default;
		$name="";
		$arg = [];
		$plain;
		$col = 30;
		$row = 10;

		foreach($dspec as $t => $v) {
			switch ($t) {
				case H_TYPE:
					$type = $v;
					break; 
				case H_NAME:
					$name = $v;
					break; 
				case H_DEFAULT:
					$default = $v;
					break; 
				case H_COL:
					$col = $v;
					break; 
				case H_ROW:
					$row = $v;
					break; 
				case H_VALUES:
					$values = $v;
					break;				
				case H_ARG:
					$arg = $v;
					break;
			}; 
		};

		$name_s = 'name = "' . $name .  '" ';
		$type_s = 'type = "' . $type .  '" ';


		if($type == H_T_PASSWORD) {$type="text";};
		switch ($type) {
			case H_T_LIST:
				$result = genList($arg,false);
				break;
			case H_T_TEXTAREA:
				$result = $textarea_s . $name_s; 
				$result = $result . $col_s . $col . '" ' ; 
				$result = $result . $row_s . $row . '" ' . $end_s ; 
       			if ($default) {$result = $result.$default;};
				$result = $result . $textarea_e_s . NL_O;
				break;
			case H_T_SUBMIT:
				$result = $button_s.NL_O; 
				break;
			case H_T_TEXT:
				$result = $input_s; 
				$result = $result . $type_s;
				$result = $result . $name_s;
				$value_s ='';
       			if ($default) {$value_s = 'value = "' . $default .  '" ';};
				$result = $result . $value_s;
				$result = $result . $end_s;
				$result = $result . NL_O;
        		break;
			case H_T_RADIO:
				$result = "";
				$separator = $dspec["separator"];
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$checked_s = "";
					if ($value == $default) {$checked_s = " checked ";};
					$result = $result . $input_s . $type_s . $name_s . $value_s . $checked_s . $end_s . $separator . NL_O;
				};
        		break;    
			case H_T_SELECT:
				$result = $select_s;
				$result = $result. $name_s . $end_s . NL_O ;
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$selected_s = "";
					if ($value == $default) {$selected_s = " selected ";};
					$result = $result . TAB_O. $option_s . $value_s . $selected_s . $end_s .$value .$option_e_s. NL_O;
				};
				$result = $result . $select_e_s. NL_O;
        		break;
			case H_T_PLAIN:
				$result = $default;
				break;
    		default:
        		$result = $plain;
		}
		if ($show) {
			echo $result;
			};
		return $result;
	}

?>