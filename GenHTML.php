<?php

	define ('NL_O', "\n");
	define ('TAB_O', "\t");
	
	require_once("ViewConstant.php");
	
	function genForm($action,$url,$dspec,$show=true){
		genformL($action,$dspec,$show,0);
	}
	
	function genFormL($action,$url,$dspecL,$show,$L) {
		$form_s   = '<form method='.$action.' action= '.$url. ' >' ;
		$form_e_s = '</form>  ';
		$end_s    = ' > '	  ;
		$tab = "";
		for($i=0;$i<$L;$i++) {$tab=$tab.TAB_O;}
		$result=$tab.$form_s.NL_O;
		foreach ($dspecL as $dspec) {
			$result=$result. genFormElemL($dspec,false,$L+1);
		}
		$result=$result.$tab.$form_e_s.NL_O;
		if ($show) {echo $result;};
		return $result;
	}
	
	function genList($dspec,$show=true){
		return(genListL($dspec,$show,0));
	}
	
	function genListL($dspecL,$show,$L){
		$list_s   = '<ul>'  ;
		$list_e_s = '</ul>';
		$element_s   = '<li>'  ;
		$element_e_s   = '</li>'  ;
		$tab = "";
		for($i=0;$i<$L;$i++) {$tab=$tab.TAB_O;}
		$tab2=$tab.TAB_O;
		
		$result = $tab.$list_s ; 
		foreach ($dspecL as $dspec) {
			$result=$result . NL_O . $tab2. $element_s. NL_O.genFormElemL($dspec,false,$L+2).$tab2.$element_e_s;
		}
		$result = $result . NL_O. $tab. $list_e_s .NL_O;
		if ($show) {echo $result;};
		return $result;
	}

	function genFormElem($dspec,$show = true)	 {
		return (genFormElemL($dspec,$show,0));
	}	
	
	function genFormElemL($dspec,$show,$L)	 {

 		$button_s   = '<input type="submit" value="Submit">';
		$textarea_s = '<textarea ';
		$textarea_e_s = '</textarea>';
		$select_s   = '<select '  ;
		$select_e_s = '</select>';
		$input_s    = '<input '   ;
		$option_s   = '<option '  ;
		$option_e_s = '</option>';
		$link_s   = '<a href='  ;
		$link_e_s = '</a>';
		$end_s      = ' >'	  ;
		$col_s	    = ' cols="'    ;
		$row_s	    = ' rows="'    ;
		
		$type="";
		$default="";
		$separator="";
		$name="";
		$action="";
		$url="";
		$arg = [];
		$plain;
		$col = 30;
		$row = 10;
		$label="";
		$tab = "";
		for($i=0;$i<$L;$i++) {$tab=$tab.TAB_O;}
		
		foreach($dspec as $t => $v) {
			switch ($t) {
				case H_TYPE:
					$type = $v;
					break; 
				case H_NAME:
					$name = $v;
					break; 				
				case H_LABEL:
					$label = $v;
					break; 
				case H_SEPARATOR:
					$separator = $v;
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
				case H_ACTION:
					$action = $v;
				case H_URL:
					$url = $v;
				case H_ARG:
					$arg = $v;
					break;
			}; 
		};

		$name_s = 'name = "' . $name .  '" ';
		$type_s = 'type = "' . $type .  '" ';


		if($type == H_T_PASSWORD) {$type="text";};
		switch ($type) {
			case H_T_LINK:
				$result = $tab.$link_s.$name.$end_s.$label.$link_e_s.NL_O;
				break;
			case H_T_LIST:
				$result = genListL($arg,false,$L);
				break;
			case H_T_FORM:
				$result = genFormL($action,$url,$arg,false,$L);
				break;
			case H_T_TEXTAREA:
				$result = $textarea_s . $name_s; 
				$result = $result . $col_s . $col . '" ' ; 
				$result = $result . $row_s . $row . '" ' . $end_s ; 
       			if ($default) {$result = $result.$default;};
				$result = $tab.$result . $textarea_e_s . NL_O;
				break;
			case H_T_SUBMIT:
				$result = $tab.$button_s.NL_O; 
				break;
			case H_T_TEXT:
				$result = $input_s; 
				$result = $result . $type_s;
				$result = $result . $name_s;
				$value_s ='';
       			if ($default) {$value_s = 'value = "' . $default .  '" ';};
				$result = $result . $value_s;
				$result = $result . $end_s;
				$result = $tab.$result . NL_O;
        		break;
			case H_T_RADIO:
				$result = "";
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$checked_s = "";
					if ($value == $default) {$checked_s = " checked ";};
					$result = $result.$tab . $input_s . $type_s . $name_s . $value_s . $checked_s . $end_s . $separator . NL_O;
				};
        		break;    
			case H_T_SELECT:
				$result = $select_s;
				$result = $tab.$result. $name_s . $end_s . NL_O ;
				foreach ($values as $value) {
					$value_s = ' value = "' . $value .  '" ';
					$selected_s = "";
					if ($value == $default) {$selected_s = " selected ";};
					$result = $result.$tab . TAB_O. $option_s . $value_s . $selected_s . $end_s .$value .$option_e_s. NL_O;
				};
				$result = $result . $tab.$select_e_s. NL_O;
        		break;
			case H_T_PLAIN:
				$result = $tab.$default.NL_O;
				break;
			case H_T_NULL:
				$result="";
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