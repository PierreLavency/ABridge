<?php


	require_once("Model.php"); 

	define('BRK', "<br/>");
	define('SPC', " ");

	define('V_C_LIST',"v_c_List");

	define('V_A_ATTR',"v_a_All_Attr");

	define('V_P_ATTR' ,"v_p_Attr");
	define('V_P_LBL'  ,"v_p_Lbl");
	define('V_P_VAL'  ,"v_p_Val");
	define('V_P_NAME' ,"v_p_Name");
	define('V_P_TYPE' ,"v_p_type");
	
	define('V_F_DSP'  ,"v_f_Dsp");
	
class View
{
	// property

	public $model;
	public $attr_lbl = [];
	public $view_spec = array([V_C_LIST,V_A_ATTR,[V_P_NAME,V_P_LBL,V_P_VAL],[V_P_ATTR=>[V_F_DSP]]]); 

	// constructors

	function __construct($model) {
		$this->model=$model; 
	   }

	// methods

	public function getLbl ($attr) {
		foreach($this->attr_lbl as $x => $lbl) {
			if ($x==$attr) {return $lbl;}
		}        	
		return NULL;
    	}

	public function getProp ($attr,$prop) {
		switch ($prop) {
    				case V_P_LBL:
	       				return $this->getLbl($attr);
        				break;
     				case V_P_VAL:	
	       				return $this->model->getVal($attr);
        				break;
    				case V_P_TYP:	
	       				return $this->model->getTyp($attr);
        				break;
    				case V_P_NAME:
        				return $attr;
        				break;
    				default:
        				return $prop;
				}    	
	}
	
	public function viewAttr($Attr,$prop,$format=[]) {
		if ($prop == V_P_ATTR) {
			$format["name"]=$Attr;
			$default = $this->model->getVal($Attr);
			if ($default) {$format["default"]=$default;}
			return $format ;
		}; 
		$x=$this->getProp ($Attr,$prop);
		$r =["plain"=>$x];
		return $r;
	}

	public function viewAttrList ($List, $Separator){
		$result=[];
		$c = count($List);
		for($i=0;$i<$c;$i++) {
				$result = $result.viewAttr($List[$i][0],$List[$i][1],$List[$i][2]);
				if($i<$c-1) {$result = $result . $Separator;}
		}
	}
	
	public function evalArg ($arg) {
		switch ($arg) {
    				case V_A_ALL_ATTR:
	       				return $this->model->getAttrList ();
        				break;
    				default:
        				return $arg;
				}    	
	}

	public function EvalSpec ($cmd,$args,$props,$formats) {
		$Args=$this->evalArg($args);
		switch ($cmd) {
    				case V_C_LIST:
					$s = count($Args);
					for($x = 0; $x < $s; $x++) {
						$attr = $Args[$x];
						echo $attr . spc . $this->getLbl($attr) . spc . $this->model->getVal($attr) . brk ;
					}; 
        				break;
    				default:
        				return 0;
				}    	
	}


	public function show () {
		$elem = $this->view_spec[0]; 
		$l = $this->model->getAttrList ();
		$s = count($l);
		for($x = 0; $x < $s; $x++) {
			$attr = $l[$x];
			echo $this->getLbl($attr) . SPC .$attr . SPC .  $this->model->getVal($attr) . BRK ; 
		}
        	return 0;
    	}



};


?>