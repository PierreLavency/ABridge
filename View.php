<?php




define("brk", "<br/>");
define("spc", " ");

define("v_c_List","v_c_List");

define("v_a_All_Attr","v_a_All_Attr");

define("v_p_Attr" ,"v_p_Attr");
define("v_p_Lbl"  ,"v_p_Lbl");
define("v_p_Val"  ,"v_p_Val");

define("v_f_Dsp"  ,"v_f_Dsp");

class View
{
	// property

	public $model;
	public $attr_lbl = array("id"=>"object reference","vnum"=>"version number");
	public $show_spec = array([v_c_List,v_a_All_Attr,[v_p_Attr,v_p_Lbl,v_p_Val],[v_p_Attr=>[v_f_Dsp]]]); 

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
    				case v_p_Lbl:
	       				return $this->getLbl($attr);
        				break;
     				case v_p_Val:	
	       				return $this->model->getVal($attr);
        				break;
    				case v_p_Typ:	
	       				return $this->model->getTyp($attr);
        				break;
    				case v_p_Attr:
        				return $attr;
        				break;
    				default:
        				return $prop;
				}    	
	}
	
	public function showProp ($Attr,$prop,$formats) {
		$x=getProp ($attr,$prop);
		return $x;
	}

	public function evalArg ($arg) {
		switch ($arg) {
    				case v_a_All_Attr:
	       				return $this->model->getAttrList ();
        				break;
    				default:
        				return $arg;
				}    	
	}

	public function ExecCmd ($cmd,$args,$props,$formats) {
		$Args=$this->evalArg($args);
		switch ($cmd) {
    				case v_c_List:
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
		$elem = $this->show_spec[0]; 
		$l = $this->model->getAttrList ();
		$s = count($l);
		for($x = 0; $x < $s; $x++) {
			$attr = $l[$x];
			echo $attr . spc . $this->getLbl($attr) . spc . $this->model->getVal($attr) . brk ; 
		}
        	return 0;
    	}



};


?>