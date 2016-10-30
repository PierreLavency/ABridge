<?php


	require_once("Model.php"); 
	require_once("ViewConstant.php");
	require_once("GenHTML.php");

	define('V_TYPE' ,"v_type");
	define('V_ELEM' ,"v_elem");
	define('V_LIST' ,"v_list");
	define('V_ARG' , "v_arg");
	define('V_OBJ' , "v_Obj");
	
	define('V_ATTR' ,"v_attr");
	define('V_CLASS',"v_class");

	define('V_PROP' ,"v_prop");
	define('V_ID' ,"v_id");
	define('V_MOD' ,"v_mod");
	
	define('V_P_INP' ,"v_p_Attr");
	define('V_P_LBL'  ,"v_p_Lbl");
	define('V_P_VAL'  ,"v_p_Val");
	define('V_P_NAME' ,"v_p_Name");
	define('V_P_TYPE' ,"v_p_type");
	define('V_P_REF' ,"v_p_ref");
	
	
class View
{
	// property

	public $model;
	public $attr_lbl = [];
	public $view_spec = []; 

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
		return $attr;
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
	
	public function evale($spec) {
		$Attr = $spec[V_ATTR];
		$prop = $spec[V_PROP];
		$res = [];
		if ($prop == V_P_INP) {
			$res[H_NAME]=$Attr;
			$default = $this->model->getVal($Attr);
			if ($default) {$res[H_DEFAULT]=$default;}
			$res[H_TYPE]=$spec[H_TYPE];
			return $res ;
		}; 
		if ($prop == V_P_REF) {
			$res[H_TYPE]=H_T_LINK;
			if (isset($spec[V_ID])){
				$id=$spec[V_ID];
				$res[H_LABEL]=$id;
			}
			else {
				$id = $this->model->getVal($Attr);			
				$res[H_LABEL]=$this->getLbl($Attr);
			}
			if (! $id) {$res = [H_TYPE =>H_T_PLAIN, H_DEFAULT=>""]; return $res;}
			$obj = $this->model->getPathMod($Attr);
			$res[H_NAME]=getRootPathString($obj,$id);	
			return $res;			
		}
		$x=$this->getProp ($Attr,$prop);
		$res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
		return $res;
	}
	
	public function evalo($dspec) {
		$name = $this->model->getModName();
		$res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$name];
		return $res;
	}
	public function subst ($spec){
		$type = $spec[V_TYPE];
		$result=[];
		switch ($type) {
 			case V_ELEM:
					$result= $this->evale($spec);
					break;
			case V_OBJ:
					$result= $this->evalo($spec);
					break;
			case V_LIST:
					$result[H_TYPE]=H_T_LIST;
					$arg=[];
					foreach($spec[V_ARG] as $elem) {
						$arg[]=$this->subst($elem);
					}
					$result[H_ARG]=$arg;
					break;
		}
		return $result;
	}

	public function show ($show=true) {
			$r=$this->subst($this->view_spec);
			$r=genFormElem($r,$show);
			return $r;
    	}
	
	public function showDefault ($show = true) {
			$i=1;
			$name = $this->model->getModName ();
			$spec=[];
			foreach ($this->model->getAllAttr() as $attr){
				$view = [[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_NAME],
						 [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_LBL ],
						 [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_TYPE],
						 [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_VAL ]];
				if ($this->model->getTyp($attr) == M_REF) {
					$view[]=[V_TYPE=>V_ELEM, V_ATTR => $attr, V_PROP => V_P_REF ];
				}
				if ($this->model->getTyp($attr) == M_CREF) {
					$view=[[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_NAME]];
					foreach($this->model->getVal($attr) as $id) {
						$view[]=[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_REF,V_ID=>$id]; // hard coded !!
					}
				}
				$spec[$i]=[V_TYPE=>V_LIST,V_ARG=>$view];
				$i++;
			}
			$spec=[V_TYPE=>V_LIST,V_ARG=>[[V_TYPE=>V_OBJ],[V_TYPE=>V_LIST,V_ARG=>$spec]]];
			$r=$this->subst($spec);
			$r=genFormElem($r,$show);
			return $r;
	}


};


?>