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
define('V_NAV', "v_nav");
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

define('V_G_CREA', "Create");
define('V_G_UPDT', "Update"); 
define('V_G_READ', "Read"); 
define('V_G_DELT', "Delete");
define('V_G_LABL', "Label");

class View
{
    // property

    protected $_model;
    protected $_attrList;
    protected $_attrLbls;
    protected $_listHtml;
    protected $_nav=[V_G_UPDT,V_G_DELT];
    protected $_attrLbl = [];
    protected $_attrProp;
    protected $_viewName = []; 

    // constructors

    function __construct($model) 
    {
        $this->_model=$model; 
    }

    // methods
    
    public function setAttrList($dspec,$viewState="") 
    {
        if ($viewState == V_G_LABL) {
            $this->_attrLbls = $dspec;
            return true;
        }
        $this->_attrList= $dspec;
        return true;
    }

    public function getAttrList($viewState="")
    {
        if ($viewState == V_G_LABL) {
            $dspec = $this->_attrLbls;
            if (is_null($dspec)) {
                $dspec = ['id'];
            }
        } else {
            $dspec = $this->_attrList;
            if (is_null($dspec)) {
                $dspec = array_diff(
                    $this->_model->getAllAttr(),
                    ['vnum','ctstp','utstp']
                );
            }               
        }
        return $dspec;
    }
    
    public function setListHtml($dspec) 
    {
        $this->_listHtml= $dspec;
        return true;
    }

    public function getListHtml ($viewState)
    {
        if ($viewState == V_G_LABL) {
            $res = H_T_CONCAT;
        } else {
            $res = $this->_listHtml;
            if (is_null($res)) {
                $res = H_T_LIST;
            }               
        }
        return $res;
    }
     
    public function setPropList($dspec) 
    {
        $this->_attrProp= $dspec;
        return true;
    }

    public function getPropList($viewState) 
    {
        if ($viewState == V_G_LABL) {
            $res = [V_P_VAL];
        } else {
            $res = $this->_attrProp;
            if (is_null($res)) {
                $res = [V_P_LBL,V_P_VAL];
            }               
        }
        return $res;
    }

    public function setNav($dspec) 
    {
        $this->_nav= $dspec;
        return true;
    }
 
    public function setLblList($dspec) 
    {
        $this->_attrLbl= $dspec;
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
                $val = $this->_model->getVal($attr);
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
    
    public function evale($spec,$viewState) 
    {
        $attr = $spec[V_ATTR];
        $prop = $spec[V_PROP];
        $res = [];
        $input=false;
        if (($viewState == V_G_CREA or $viewState == V_G_UPDT) 
            and $prop==V_P_VAL) {
            if ($this->_model->isMdtr($attr) or $this->_model->isOptl($attr)) {
                $res[H_NAME]=$attr;
                $default=null;
                if (isset($_POST[$attr])) {
                    $default= $_POST[$attr];
                } else {
                    if ($viewState == V_G_CREA) {
                        $default=$this->_model->getDflt($attr);
                    }               
                    if ($viewState == V_G_UPDT) {
                        $default=$this->_model->getVal($attr);
                    }       
                }
                if ($default) {
                    $res[H_DEFAULT]=$default;
                }
                $res[H_TYPE]=H_T_TEXT;
                if ($this->_model->getTyp($attr)== M_CODE) {
                    $vals=$this->_model->getValues($attr);
                    $values=[];
                    if (count($vals)>2) {//bof
                        $res[H_TYPE]=H_T_SELECT;
                        if (!$this->_model->isMdtr($attr)) {
                            $values[] = ["",""];
                        }
                    } else {
                        $res[H_TYPE]=H_T_RADIO;
                    }
                    $mod = $this->_model->getRefMod($attr);
                    foreach ($vals as $v) {
                        $m = new Model($mod, $v);
                        $vw = new View($m);
                        $l = $vw->show(V_G_LABL, false);
                        $r = [$v,$l];
                        $values[]=$r;
                    }
                    $res[H_VALUES]=$values;
                    if (count($vals)>2) {//bof
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
            } else {
                $id=$this->_model->getVal($attr);
            }
            if (! $id) {
                return 0;
            }
            $mod = $this->_model->getRefMod($attr);
            $m = new Model($mod, $id);
            $v = new View($m);
            $l = $v->show(V_G_LABL, false);
            $res[H_LABEL]=$l;
            $res[H_NAME]=refPath($mod, $id); 
            return $res;            
        }   
        $x=$this->getProp($attr, $prop);
        if ($prop==V_P_VAL) {
            $typ = $this->_model->getTyp($attr);
            if ($typ==M_REF) {
                return [];
            }
            if ($typ==M_CODE and (!is_null($x))) {
                $mod = $this->_model->getRefMod($attr);
                $id = (int) $x;
                $m = new Model($mod, $id);
                $v = new View($m);
                $x = $v->show(V_G_LABL, false);
            }
        }
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
        return $res;
    }
    
    public function evalo($dspec,$viewState) 
    {
        $name = $this->_model->getModName();
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$name];
        return $res;
    }
    
    public function subst($spec,$viewState)
    {
        $type = $spec[V_TYPE];
        $result=[];
        switch ($type) {
            case V_ELEM:
                    $result= $this->evale($spec, $viewState);
                break;
            case V_OBJ:
                    $result= $this->evalo($spec, $viewState);
                break;
            case V_LIST:
                    $result[H_TYPE]=$this->getListHtml($viewState);
                    $result[H_SEPARATOR]= ' ';
                    $arg=[];
                    foreach ($spec[V_ARG] as $elem) {
                        $r=$this->subst($elem, $viewState);
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
                    $result[H_HIDDEN]=$viewState;
                    $result[H_URL]=$path;                   
                    $arg=[];
                    foreach ($spec[V_ARG] as $elem) {
                        $r=$this->subst($elem, $viewState);
                        if ($r) {
                            $arg[]=$r;
                        }
                    }
                    $result[H_ARG]=$arg;
                break;
            case V_NAV:
                    if ($viewState == V_G_CREA 
                    or  $viewState == V_G_DELT 
                    or  $viewState == V_G_UPDT) {
                        $result[H_TYPE]=H_T_SUBMIT;
                        $result[H_LABEL]=$this->getLbl(H_T_SUBMIT);
                    }       
                    if ($viewState == V_G_READ) {
                        if (count($this->_nav)) {
                            $result[H_TYPE]=H_T_LIST;
                            $res[H_TYPE]=H_T_LINK;
                            $p=refPath(
                                $this->_model->getModName(), 
                                $this->_model->getId()
                            );
                            foreach ($this->_nav as $nav) {
                                $res[H_LABEL]=$this->getLbl($nav);
                                $res[H_NAME]="'".$p.'?View='.$nav."'";
                                $arg[]=$res;
                            }
                            $result[H_ARG]=$arg;
                        }
                    }
                break;
            case V_ERROR:
                    $result[H_TYPE]=H_T_PLAIN;
                    $result[H_DEFAULT]=$spec[V_STRING];
                break;                              
        }
        return $result;
    }

    public function show($viewState,$show = true) 
    {
        $labels = 
        [ 'Person'  => ['SurName','Name'],
          'Student' => ['SurName','Name'],
          'Cours'   => ['Name'],
          'CodeValue'=>['Name']];

        if ($viewState == V_G_LABL) {
            if (isset($labels[$this->_model->getModName()])) {
                $x = $labels[$this->_model->getModName()];
                $this->setAttrList($x, V_G_LABL);
            }
        }
        
        $r = $this->buildView($viewState);
        $r=genFormElem($r, $show);
        return $r;
    }
    
    public function postVal()
    { 
        foreach ($this->getAttrList() as $attr) { 
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
    
    public function buildView($viewState) 
    {
        $i=0;
        $name = $this->_model->getModName();
        $spec=[];
        foreach ($this->getAttrList($viewState) as $attr) {
            $view =[];
            foreach ($this->getPropList($viewState) as $prop) {
                $view[] = [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => $prop];
            }
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
            if ($viewState == V_G_LABL) {
                $spec = array_merge($spec, $view);
            } else {
                $spec[$i]=[V_TYPE=>V_LIST,V_ARG=>$view];
                $i++;
            }

        }
        if ($viewState == V_G_LABL) {
            $specf = [V_TYPE=>V_LIST,V_ARG=>$spec];
            $r=$this->subst($specf, $viewState);
            return $r;
        }
        $arg = [];
        $arg[]= [V_TYPE=>V_OBJ];
        $arg[]= [V_TYPE=>V_LIST,V_ARG=>$spec];
        $arg[]= [V_TYPE=>V_NAV];
        if ($this->_model->isErr()) {
            $e = $this->viewErr();
            $arg[]=$e;
        }
        $speci = [V_TYPE=>V_LIST,V_ARG=>$arg];
        $specf = $speci;
        if ($viewState == V_G_CREA 
        or  $viewState == V_G_DELT 
        or  $viewState == V_G_UPDT) {
            $specf = [V_TYPE=>V_FORM,V_ARG=>[$speci]];
        }
        $r=$this->subst($specf, $viewState);
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

