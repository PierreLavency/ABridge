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
	define('V_BTN' ,"v_btn");
	define('V_FORM' ,"v_form");
	define('V_PLAIN' ,"v_plain"); // not sure needed 
	define('V_ERROR' ,"v_error"); 
		
	define('V_PROP' ,"v_prop");
	define('V_ID' ,"v_id");
	define('V_ACTION' ,"v_action");
	define('V_STRING' ,"v_string");
		
	define('V_P_INP' ,"v_p_Attr");
	define('V_P_LBL'  ,"v_p_Lbl");
	define('V_P_VAL'  ,"v_p_Val");
	define('V_P_NAME' ,"v_p_Name");
	define('V_P_TYPE' ,"v_p_type");
	define('V_P_REF' ,"v_p_ref");
	
	define('V_G_VIEW',"v_g_view");
	define('V_G_CREA',"v_g_crea");
	
class View
{
	// property

	public $model;
	public $method;
	public $attr_list;
	public $attr_lbl = ['Mother'=>'Mere'];
	public $viewName = []; 

	// constructors

	function __construct($model) {
		$this->model=$model; 
		$this->attr_list = $this->model->getAllAttr();
	   }

	// methods
	
	public function setAttrList($dspec) {
		$this->attr_list= $dspec;
		return true;
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
	
	public function evale($spec,$gen) {
		$Attr = $spec[V_ATTR];
		$prop = $spec[V_PROP];
		$res = [];
		$input=false;
		if ($gen==V_G_CREA and $prop==V_P_VAL){
			if ($this->model->isMdtr($Attr) or $this->model->isOptl($Attr)) {
				$res[H_NAME]=$Attr;
				$default = $this->model->getVal($Attr); 
				if (isset($_POST[$Attr])){$default= $_POST[$Attr];}
				if ($default) {$res[H_DEFAULT]=$default;}
				$res[H_TYPE]=H_T_TEXT;
				if ($this->model->getTyp($Attr)== M_CODE) {
					$vals=$this->model->getValues($Attr);
					$res[H_VALUES]=$vals;
					if (count($vals)>2) {$res[H_TYPE]=H_T_SELECT;}
					else {$res[H_TYPE]=H_T_RADIO;}		
				}
				return $res ;
			}
		}
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
			if (! $id) {return 0;}
			$mod = $this->model->getRefMod($Attr);
			$res[H_NAME]=refPath($mod,$id);	
			return $res;			
		}	
		$x=$this->getProp ($Attr,$prop);
		$res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
		return $res;
	}
	
	public function evalo($dspec,$gen) {
		$name = $this->model->getModName();
		$res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$name];
		return $res;
	}
	
	public function subst ($spec,$gen){
		$type = $spec[V_TYPE];
		$result=[];
		switch ($type) {
 			case V_ELEM:
					$result= $this->evale($spec,$gen);
					break;
			case V_OBJ:
					$result= $this->evalo($spec,$gen);
					break;
			case V_LIST:
					$result[H_TYPE]=H_T_LIST;
					$arg=[];
					foreach($spec[V_ARG] as $elem) {
						$r=$this->subst($elem,$gen);
						if($r) {$arg[]=$r;}
					}
					$result[H_ARG]=$arg;
					break;
			case V_FORM:
					$result[H_TYPE]=H_T_FORM;
					$path = refPath($this->model->getModName(),$this->model->getId());
					$result[H_ACTION]="POST";
					$result[H_URL]=$path;					
					$arg=[];
					foreach($spec[V_ARG] as $elem) {
						$r=$this->subst($elem,$gen);
						if($r) {$arg[]=$r;}
					}
					$result[H_ARG]=$arg;
					break;
			case V_BTN:
					if($gen == V_G_CREA){
						$result[H_TYPE]=H_T_SUBMIT;
					}
					else {
						$result[H_TYPE]=H_T_LINK;
						$result[H_LABEL]=$this->model->getId();
						$result[H_NAME]=refPath($this->model->getModName(),$this->model->getId()).'?form=true';
					}
					break;
			case V_ERROR:
					$result[H_TYPE]=H_T_PLAIN;
					$result[H_DEFAULT]=$spec[V_STRING];
					break;								
		}
		return $result;
	}

	public function show ($method,$exists,$show = true) {
		if ($method =='POST') {return ($this->showDefaultG($show,V_G_CREA));}
		if ($method =='GET' and (!  $exists)) {return ($this->showDefaultG($show,V_G_CREA));}
		if ($method =='GET' and (isset($_GET["form"]))) {return ($this->showDefaultG($show,V_G_CREA));}
		return ($this->showDefaultG($show,V_G_VIEW));
	}
	
	public function postVal(){ // maybe be not really MVC but 
		foreach ($this->attr_list as $attr) {
			if ($this->model->isMdtr($attr) or $this->model->isOptl($attr)) {
				if (isset($_POST[$attr])){
					$val= $_POST[$attr];
					if ($val) {
						$typ=$this->model->getTyp($attr);
						$Val = convertString($val,$typ);
						$this->model->setVal($attr,$Val);
					}
				}
			}
		}
	}
	
	public function showDefaultG ($show,$gen) {
			$i=1;
			$name = $this->model->getModName ();
			$spec=[];
			foreach ($this->attr_list as $attr){
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
						$view[]=[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_REF,V_ID=>$id];
					}
				}
				$spec[$i]=[V_TYPE=>V_LIST,V_ARG=>$view];
				$i++;
			}
			$speci=[V_TYPE=>V_LIST,V_ARG=>[[V_TYPE=>V_OBJ],[V_TYPE=>V_LIST,V_ARG=>$spec],[V_TYPE=>V_BTN]]];
			if ($this->model->isErr()){
				$e = $this->viewErr();
				$speci=[V_TYPE=>V_LIST,V_ARG=>[[V_TYPE=>V_OBJ],[V_TYPE=>V_LIST,V_ARG=>$spec],[V_TYPE=>V_BTN],$e]];
			}
			$specf = $speci;
			if ($gen == V_G_CREA) {
				$specf = [V_TYPE=>V_FORM,V_ARG=>[$speci]];		// html specific !
			}
			$r=$this->subst($specf,$gen);
			$r=genFormElem($r,$show);
			return $r;
	}

	public function viewErr() {
			$log = $this->model->getErrLog();
			$c=$log->logSize();
			if (!$c) {return 0;}
			$result=[];
			for ($i=0;$i<$c;$i++){$r = $log->getLine($i);$result[$i]=[V_TYPE=>V_ERROR,V_STRING=>$r];}
			$result = [V_TYPE =>V_LIST,V_ARG=>$result];
			return $result;
	}

};


?>