<?php

	define ('NL_O', "\n");
	define ('TAB_O', "\t");
	
	define ('H_TYPE', "h_type");
	
	
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
		$plain;
		$col = 30;
		$row = 10;


		foreach($dspec as $t => $v) {
			switch ($t) {
				case "type":
					$type = $v;
					break; 
				case "name":
					$name = $v;
					break; 
				case "default":
					$default = $v;
					break; 
				case "col":
					$col = $v;
					break; 
				case "row":
					$row = $v;
					break; 
				case "plain":
					$plain = $v;
					break; 
			}; 
		};


		$name_s = 'name = "' . $name .  '" ';
		$type_s = 'type = "' . $type .  '" ';


		if($type == "password") {$type="text";};
		switch ($type) {
			case "textarea":
				$result = $textarea_s . $name_s; 
				$result = $result . $col_s . $col . '" ' ; 
				$result = $result . $row_s . $row . '" ' . $end_s ; 
       			if ($default) {$result = $result.$default;};
				$result = $result . $textarea_e_s . NL_O;
				break;
			case "submit":
				$result = $button_s.NL_O; 
				break;
			case "text":
				$result = $input_s; 
				$result = $result . $type_s;
				$result = $result . $name_s;
				$value_s ='';
       			if ($default) {$value_s = 'value = "' . $default .  '" ';};
				$result = $result . $value_s;
				$result = $result . $end_s;
				$result = $result . NL_O;
        		break;
			case "radio":
				$result = "";
				$values = $dspec["values"];
				$separator = $dspec["separator"];
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$checked_s = "";
					if ($value == $default) {$checked_s = " checked ";};
					$result = $result . $input_s . $type_s . $name_s . $value_s . $checked_s . $end_s . $separator . NL_O;
				};
        		break;    
			case "select":
				$result = $select_s;
				$result = $result. $name_s . $end_s . NL_O ;
				$values = $dspec["values"];
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$selected_s = "";
					if ($value == $default) {$selected_s = " selected ";};
					$result = $result . TAB_O. $option_s . $value_s . $selected_s . $end_s .$value .$option_e_s. NL_O;
				};
				$result = $result . $select_e_s. NL_O;
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