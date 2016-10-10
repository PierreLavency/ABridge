<?php

	function displayAttr($Model, $Attr, $dspec) {
		$dspec["name"]=$Attr;
		$default = $Model->getVal($Attr);
		if ($default) {$dspec["default"]=$default;}
		genHtml($dspec);
	}; 

	function genHtml($dspec)	 {

		$nl_o 	    = "\n";
		$tab_o 	    = "\t";

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
		

		$type;
		$default;
		$name;
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
				$result = $result . $textarea_e_s . $nl_o;
				break;
			case "submit":
				$result = $button_s.$nl_o; 
				break;
			case "text":
				$result = $input_s; 
				$result = $result . $type_s;
				$result = $result . $name_s;
				$value_s ='';
       				if ($default) {$value_s = 'value = "' . $default .  '" ';};
				$result = $result . $value_s;
				$result = $result . $end_s;
				$result = $result . $nl_o;
        			break;
			case "radio":
				$result = "";
				$values = $dspec["values"];
				$separator = $dspec["separator"];
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$checked_s = "";
					if ($value == $default) {$checked_s = " checked ";};
					$result = $result . $input_s . $type_s . $name_s . $value_s . $checked_s . $end_s . $separator . $nl_o;
				};
        			break;    
			case "select":
				$result = $select_s;
				$result = $result. $name_s . $end_s . $nl_o;
				$values = $dspec["values"];
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$selected_s = "";
					if ($value == $default) {$selected_s = " selected ";};
					$result = $result . $tab_o. $option_s . $value_s . $selected_s . $end_s .$value .$option_e_s. $nl_o;
				};
				$result = $result . $select_e_s. $nl_o;
        			break;    
    			default:
        			$result = 0;
		}
		echo $result;

	}

?>