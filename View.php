<?php


require_once("Model.php"); 
require_once("ViewConstant.php");
require_once("GenHTML.php");

define('V_TYPE', "v_type");
define('V_ELEM', "v_elem");
define('V_LIST', "v_list");
define('V_ARG', "v_arg");
define('V_OBJ', "v_Obj");
define('V_ATTR', "v_attr");
define('V_BTN', "v_btn");
define('V_FORM', "v_form");
define('V_PLAIN', "v_plain"); // not sure needed 
define('V_ERROR', "v_error"); 
    
define('V_PROP', "v_prop");
define('V_ID', "v_id");
define('V_ACTION', "v_action");
define('V_STRING', "v_string");
    
define('V_P_INP', "v_p_Attr");
define('V_P_LBL', "v_p_Lbl");
define('V_P_VAL', "v_p_Val");
define('V_P_NAME', "v_p_Name");
define('V_P_TYPE', "v_p_type");
define('V_P_REF', "v_p_ref");

define('V_G_VIEW', "v_g_view");
define('V_G_CREA', "v_g_crea");
    
class View
{
    // property

    protected $_model;
    protected $_method='GET';
    protected $_exists=true;
    protected $_attrList;
    protected $_attrLbl = [];
    protected $_viewName = []; 

    // constructors

    function __construct($model) 
    {
        $this->_model=$model; 
        $this->_attrList = $this->_model->getAllAttr();
    }

    // methods
    
    public function setAttrList($dspec) 
    {
        $this->_attrList= $dspec;
        return true;
    }
    
    public function getLbl($attr) 
    {
        foreach ($this->_attrLbl as $x => $lbl) {
            if ($x==$attr) {
                return $lbl;
            }
        }           
        return $attr;
    }
        
    public function getProp($attr,$prop) 
    {
        switch ($prop) {
            case V_P_LBL:
                return $this->getLbl($attr);
                break;
            case V_P_VAL:
                $type = $this->_model->getTyp($attr);
                $val = $this->_model->getVal($attr);
                if ($type == M_CREF) {
                    $val = implode(',', $val);
                }
                return $val;
                break;
            case V_P_TYPE:  
                return $this->_model->getTyp($attr);
                break;
            case V_P_NAME:
                return $attr;
                break;
            default:
                return 0;
        }       
    }
    
    public function evale($spec,$gen) 
    {
        $attr = $spec[V_ATTR];
        $prop = $spec[V_PROP];
        $res = [];
        $input=false;
        if ($gen==V_G_CREA and $prop==V_P_VAL) {
            if ($this->_model->isMdtr($attr) or $this->_model->isOptl($attr)) {
                $res[H_NAME]=$attr;
                $default=null;
                if ($this->_method=='GET') {
                    $default = $this->_model->getVal($attr);
                    if ((! $this->_exists) and is_null($default)) {
                        $default=$this->_model->getDflt($attr);
                    }
                }
                if ($this->_method=='POST') {
                    if (isset($_POST[$attr])) {
                        $default= $_POST[$attr];
                    }
                }
                if ($default) {
                    $res[H_DEFAULT]=$default;
                }
                $res[H_TYPE]=H_T_TEXT;
                if ($this->_model->getTyp($attr)== M_CODE) {
                    $vals=$this->_model->getValues($attr);
                    $res[H_VALUES]=$vals;
                    if (count($vals)>2) {
                        $res[H_TYPE]=H_T_SELECT;
                    } else {
                        $res[H_TYPE]=H_T_RADIO;
                    }      
                }
                return $res ;
            }
        }
        if ($prop == V_P_REF) {
            $res[H_TYPE]=H_T_LINK;
            if (isset($spec[V_ID])) {
                $id=$spec[V_ID];
                $res[H_LABEL]=$id;
            } else {
                $id = $this->_model->getVal($attr);          
                $res[H_LABEL]=$this->getLbl($attr);
            }
            if (! $id) {
                return 0;
            }
            $mod = $this->_model->getRefMod($attr);
            $res[H_NAME]=refPath($mod, $id); 
            return $res;            
        }   
        $x=$this->getProp($attr, $prop);
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
        return $res;
    }
    
    public function evalo($dspec,$gen) 
    {
        $name = $this->_model->getModName();
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$name];
        return $res;
    }
    
    public function subst($spec,$gen,$form)
    {
        $type = $spec[V_TYPE];
        $result=[];
        switch ($type) {
            case V_ELEM:
                    $result= $this->evale($spec, $gen);
                break;
            case V_OBJ:
                    $result= $this->evalo($spec, $gen);
                break;
            case V_LIST:
                    $result[H_TYPE]=H_T_LIST;
                    $arg=[];
                    foreach ($spec[V_ARG] as $elem) {
                        $r=$this->subst($elem, $gen, $form);
                        if ($r) {
                            $arg[]=$r;
                        }
                    }
                    $result[H_ARG]=$arg;
                break;
            case V_FORM:
                    $result[H_TYPE]=H_T_FORM;
                    $path = refPath(
                        $this->_model->getModName(), 
                        $this->_model->getId()
                    );
                    $result[H_ACTION]="POST";
                    if ($gen == V_G_VIEW) {
                        $result[H_HIDDEN]="Del";
                    }
                    if ($gen == V_G_CREA and $this->_exists) {
                        $result[H_HIDDEN]="Mod";
                    }
                    if ($gen == V_G_CREA and  ! $this->_exists) {
                        $result[H_HIDDEN]="Crt";
                    }
                    $result[H_URL]=$path;                   
                    $arg=[];
                    foreach ($spec[V_ARG] as $elem) {
                        $r=$this->subst($elem, $gen, $form);
                        if ($r) {
                            $arg[]=$r;
                        }
                    }
                    $result[H_ARG]=$arg;
                break;
            case V_BTN:
                    if ($form) {
                        $result[H_TYPE]=H_T_SUBMIT;
                    } else {
                        $result[H_TYPE]=H_T_LIST;
                        $res[H_TYPE]=H_T_LINK;
                        $res[H_LABEL]='Mod';
                        $p=refPath(
                            $this->_model->getModName(), 
                            $this->_model->getId()
                        );
                        $res[H_NAME]="'".$p.'?View=Mod'."'";
                        $arg[]=$res;
                        $res[H_TYPE]=H_T_LINK;
                        $res[H_LABEL]='Del';
                        $res[H_NAME]="'".$p.'?View=Del'."'";
                        $arg[]=$res;
                        $result[H_ARG]=$arg;
                    }
                break;
            case V_ERROR:
                    $result[H_TYPE]=H_T_PLAIN;
                    $result[H_DEFAULT]=$spec[V_STRING];
                break;                              
        }
        return $result;
    }

    public function show($method,$exists,$show = true) 
    {
        $this->_method=$method;
        $this->_exists=$exists;
        if ($method =='POST') {
            return ($this->showDefaultG($show, V_G_CREA, true));
        }
        if ($method =='GET' and (! $exists)) {
            return ($this->showDefaultG($show, V_G_CREA, true));
        }
        if ($method =='GET' and (isset($_GET["View"]))) {
            $view = $_GET["View"];
            if ($view =='Mod' ) {
                return ($this->showDefaultG($show, V_G_CREA, true));
            }
            if ($view =='Del' ) {
                return ($this->showDefaultG($show, V_G_VIEW, true));
            }
        }
        return ($this->showDefaultG($show, V_G_VIEW, false));
    }
    
    public function postVal()
    { 
        foreach ($this->_attrList as $attr) {
            if ($this->_model->isMdtr($attr) or $this->_model->isOptl($attr)) {
                if (isset($_POST[$attr])) {
                    $val= $_POST[$attr];
                    $typ= $this->_model->getTyp($attr);
                    $valC = convertString($val, $typ);
                    $this->_model->setVal($attr, $valC);
                }
            }
        }
    }
    
    public function showDefaultG ($show,$gen,$form) 
    {
        $i=1;
        $name = $this->_model->getModName();
        $spec=[];
        foreach ($this->_attrList as $attr) {
            $view = [[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_NAME],
//                       [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_LBL ],
//                       [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_TYPE],
                     [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_VAL ]];
            if ($this->_model->getTyp($attr) == M_REF) {
                $view[]=[V_TYPE=>V_ELEM, V_ATTR => $attr, V_PROP => V_P_REF ];
            }
            if ($this->_model->getTyp($attr) == M_CREF) {
                $view=[[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_NAME]];
                foreach ($this->_model->getVal($attr) as $id) {
                    $view[]=[
                                V_TYPE=>V_ELEM,V_ATTR => $attr,
                                V_PROP => V_P_REF,V_ID=>$id];
                }
            }
            $spec[$i]=[V_TYPE=>V_LIST,V_ARG=>$view];
            $i++;
        }
        $speci=[V_TYPE=>V_LIST,V_ARG=>[
                                        [V_TYPE=>V_OBJ],
                                        [V_TYPE=>V_LIST,V_ARG=>$spec],
                                        [V_TYPE=>V_BTN]]];
        if ($this->_model->isErr()) {
            $e = $this->viewErr();
            $speci=[V_TYPE=>V_LIST,V_ARG=>[
                                        [V_TYPE=>V_OBJ],
                                        [V_TYPE=>V_LIST,V_ARG=>$spec],
                                        [V_TYPE=>V_BTN],
                                        $e]];
        }
        $specf = $speci;
        if ($form) {
            $specf = [V_TYPE=>V_FORM,V_ARG=>[$speci]];      // html specific !
        }
        $r=$this->subst($specf, $gen, $form);
        $r=genFormElem($r, $show);
        return $r;
    }

    public function viewErr() 
    {
            $log = $this->_model->getErrLog();
            $c=$log->logSize();
            if (!$c) {
                return 0;
            }
            $result=[];
            for ($i=0;$i<$c;$i++) {
                $r = $log->getLine($i);
                $result[$i]=[V_TYPE=>V_ERROR,V_STRING=>$r];
            }
            $result = [V_TYPE =>V_LIST,V_ARG=>$result];
            return $result;
    }

};

