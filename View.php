<?php


	require_once("Model.php"); 
	require_once("ViewConstant.php");
	require_once("GenHTML.php");

	define('V_VIEW' ,"v_VIEW");
	
	define('V_ATTR' ,"v_attr");
	define('V_PROP' ,"v_prop");
	
	define('V_P_INP' ,"v_p_Attr");
	define('V_P_LBL'  ,"v_p_Lbl");
	define('V_P_VAL'  ,"v_p_Val");
	define('V_P_NAME' ,"v_p_Name");
	define('V_P_TYPE' ,"v_p_type");
	
	
class View
{
	// property

	public $model;
	public $attr_lbl = [];
	public $view_spec = []; 
/*
    V_VIEW => [V_ATTR =>xx; V_PROP => V_P_LBL] 
    V_VIEW => [V_ATTR => xx; V_PROP => V_P_INP] 
	
*/
	// constructors

	function __construct($model) {
		$this->model=$model; 
	   }

	// methods

	public function setSpec($dspec) {
		$this->view_spec = $dspec;
		return $dspec;
	}
	
	
	public function getLbl ($attr) {
		foreach($this->attr_lbl as $x => $lbl) {
			if ($x==$attr) {return $lbl;}
		}        	
		return "no label defined";
    	}
		
	public function getProp ($attr,$prop) {
		switch ($prop) {
    		case V_P_LBL:
				return $this->getLbl($attr);
        		break;
     		case V_P_VAL:
				$type = $this->model->getTyp($attr);
				$val = $this->model->getVal($attr);
				if ($type == M_CREF) {$val = implode(',',$val);}
	    		return $val;
        		break;
    		case V_P_TYPE:	
	    		return $this->model->getTyp($attr);
        		break;
    		case V_P_NAME:
        		return $attr;
        		break;
    		default:
        		return 0;
		}    	
	}
	
	public function evalp($spec) {
		$Attr = $spec[V_ATTR];
		$prop = $spec[V_PROP];
		$res = [];
		if ($prop == V_P_INP) {
			$res[H_NAME]=$Attr;
			$default = $this->model->getVal($Attr);
			if ($default) {$res[H_DEFAULT]=$default;}
			return $res ;
		}; 
		$x=$this->getProp ($Attr,$prop);
		$res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
		return $res;
	}
	
	public function evala($dspec){
		for ($i=0; $i<count($dspec);$i++){
			$dspec[$i] = $this->subst ($dspec[$i]);
		}
		return $dspec;
	}
	
	public function subst ($spec){
		foreach ($spec as $key => $val) {
			switch ($key) {
				case H_ARG:
					$spec[$key]= $this->evala($val);
					break;
 				case V_VIEW:
					$res = $this->evalp($val);
					foreach ($res as $keyr => $valr){
						$spec[$keyr]=$valr;
					}
					unset($spec[$key]);
					break;
				defaut:
					break;
			}
		}
		return $spec;
	}

	public function show ($show=true) {
			$r=$this->subst($this->view_spec);
			$r=genFormElem($r,$show);
			return $r;
    	}
	
	public function showDefault ($show = true) {
			$i=1;
			$name = $this->model->getModName ();
			$spec=[[H_TYPE=>H_T_PLAIN,H_DEFAULT=> "Attrinutes of $name"]];
			foreach ($this->model->getAllAttr() as $attr){
				$spec[$i]=[H_TYPE=>H_T_LIST,H_ARG=>[
						[V_VIEW =>[V_ATTR => $attr, V_PROP => V_P_NAME]],
						[H_TYPE=>H_T_LIST,H_ARG=>[[V_VIEW =>[V_ATTR => $attr, V_PROP => V_P_LBL ]],
												  [V_VIEW =>[V_ATTR => $attr, V_PROP => V_P_TYPE]],
												  [V_VIEW =>[V_ATTR => $attr, V_PROP => V_P_VAL ]]]]]];
				$i++;
			}
			$spec=[H_TYPE=>H_T_LIST,H_ARG=>$spec];
			$r=$this->subst($spec);
			$r=genFormElem($r,$show);
			return $r;
	}


};


?>