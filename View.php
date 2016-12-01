<?php

require_once("Model.php"); 
require_once("ViewConstant.php");
require_once("GenHTML.php");

define('V_TYPE', "v_type");
define('V_ELEM', "v_elem");
define('V_LIST', "v_list");
define('V_ARG', "v_arg");
define('V_OBJ', "v_Obj");
define('V_NAV', "v_nav");
define('V_ERROR', "v_error"); 
define('V_FORM', "v_form");

define('V_ID', "v_id");
define('V_STRING', "v_string");

// V_ELEM
define('V_ATTR', "v_attr");
define('V_REP', "v_rep");  // should be set to the H_T_TYPE;
    
define('V_PROP', "v_prop");   
define('V_P_INP', "v_p_Attr");
define('V_P_LBL', "v_p_Lbl");
define('V_P_VAL', "v_p_Val");
define('V_P_NAME', "v_p_Name");
define('V_P_TYPE', "v_p_type");
define('V_P_REF', "v_p_ref");

define('V_S_CREA', "Create");
define('V_S_UPDT', "Update"); 
define('V_S_READ', "Read"); 
define('V_S_DELT', "Delete");
define('V_S_REF', "reference");
define('V_S_CREF', "creference");

class View
{
    // property

    protected $_model;
    protected $_attrList;
    protected $_attrRef;
    protected $_attrCref;
    protected $_listHtml;
    protected $_nav=[V_S_UPDT,V_S_DELT];
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
        if ($viewState == V_S_REF) {
            $this->_attrRef = $dspec;
            return true;
        }
        if ($viewState == V_S_CREF) {
            $this->_attrCref = $dspec;
            return true;
        }
        $this->_attrList= $dspec;
        return true;
    }

    public function getAttrList($viewState)
    {
        switch ($viewState) {
            case V_S_REF :
                $dspec = $this->_attrRef;
                if (is_null($dspec)) {
                    $dspec = ['id'];
                }
                return $dspec;
            case V_S_CREF :
                $dspec = $this->_attrCref;
                if (is_null($dspec)) {
                    $dspec = [];
                    $res = array_diff(
                        $this->_model->getAllAttr(),
                        ['vnum','ctstp','utstp']
                    );
                    $ref = $this->getAttrList(V_S_REF);
                    $key = array_search('id', $ref);
                    if ($key!==false) {
                        unset($ref[$key]);
                    } 
                    $res = array_diff($res, $ref);       
                    foreach ($res as $attr) {
                        if ($this->_model->getTyp($attr) != M_CREF) {
                            $dspec[]=$attr;
                        }
                    }
                }
                return $dspec ;   
            default : 
                $dspec = $this->_attrList;
                if (is_null($dspec)) {
                    $dspec = array_diff(
                        $this->_model->getAllAttr(),
                        ['vnum','ctstp','utstp']
                    );
                }
                return $dspec;
        }
    }
    
    public function setListHtml($dspec) 
    {
        $this->_listHtml= $dspec;
        return true;
    }

    public function getListHtml ($viewState)
    {
        if ($viewState == V_S_REF) {
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
        switch($viewState) {
            case V_S_REF :
                $res = [V_P_VAL];
                return $res;
            case V_S_CREF : 
                $res = [V_P_VAL];
                return $res;
            default :
                $res = $this->_attrProp;
                if (is_null($res)) {
                    $res = [V_P_LBL,V_P_VAL];
                }
                return $res;
        }
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
        $typ = $this->_model->getTyp($attr);
        $prop = $spec[V_PROP];
        $res = [];
        if (($viewState == V_S_CREA or $viewState == V_S_UPDT) 
            and $prop==V_P_VAL and 
            ($this->_model->isMdtr($attr) or $this->_model->isOptl($attr))) {
            $res[H_NAME]=$attr;
            $default=null;
            if (isset($_POST[$attr])) {
                $default= $_POST[$attr];
            } else {
                if ($viewState == V_S_CREA) {
                    $default=$this->_model->getDflt($attr);
                }               
                if ($viewState == V_S_UPDT) {
                    $default=$this->_model->getVal($attr);
                }       
            }
            if ($default) {
                $res[H_DEFAULT]=$default;
            }
            $res[H_TYPE]=H_T_TEXT;
            if ($typ == M_CODE) {
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
                foreach ($vals as $v) {
                    $m = $this->_model->getCode($attr, (int) $v);
                    $vw = new View($m);
                    $l = $vw->show(V_S_REF, false);
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
        $x=$this->getProp($attr, $prop);
        if ($prop==V_P_VAL) {
            if ($attr == 'id' and $viewState != V_S_REF) {
                $res[H_TYPE]=H_T_LINK;
                $res[H_LABEL]=$this->show(V_S_REF, false);
                $res[H_NAME]=$this->_model->getPath();
                return $res;
            }
            if ($typ==M_CREF) {     
                $id=$spec[V_ID];
                $m = $this->_model->getCref($attr, $id);
                $v = new View($m);
                $res = $v->buildView(V_S_CREF);
                return $res; 
            }
            if ($typ==M_REF) {
                $res[H_TYPE]=H_T_LINK;
                $m=$this->_model->getRef($attr);
                if (is_null($m)) {
                    return 0;
                }
                $v = new View($m);
                $res[H_LABEL]=$v->show(V_S_REF, false);
                $res[H_NAME]=$m->getPath();
                return $res;        
            }
            if ($typ==M_CODE and (!is_null($x))) {
                $m=$this->_model->getCode($attr, (int) $x);
                $v = new View($m);
                $x = $v->show(V_S_REF, false);
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
                    $path = $this->_model->getPath(); 
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
                    if ($viewState == V_S_CREA 
                    or  $viewState == V_S_DELT 
                    or  $viewState == V_S_UPDT) {
                        $result[H_TYPE]=H_T_SUBMIT;
                        $result[H_LABEL]=$this->getLbl(H_T_SUBMIT);
                    }       
                    if ($viewState == V_S_READ) {
                        if (count($this->_nav)) {
                            $result[H_TYPE]=H_T_LIST;
                            $res[H_TYPE]=H_T_LINK;
                            $p=$this->_model->getPath(); 
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
        $r = $this->buildView($viewState);
        $r=genFormElem($r, $show);
        return $r;
    }
    
    public function buildView($viewState) 
    {
        $labels = 
        [ 'Person'  => ['SurName','Name'],
          'Student' => ['SurName','Name'],
          'Cours'   => ['Name'],
          'CodeValue'=>['Name']];

        if (isset($labels[$this->_model->getModName()])) {
            $x = $labels[$this->_model->getModName()];
            $this->setAttrList($x, V_S_REF);
        }
        
        $i=0;
        $name = $this->_model->getModName();
        $spec=[];
        foreach ($this->getAttrList($viewState) as $attr) {
            $view =[];
            foreach ($this->getPropList($viewState) as $prop) {
                $view[] = [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => $prop];
            }
            if ($this->_model->getTyp($attr) == M_CREF) {
                $view=[[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_NAME]];
                foreach ($this->_model->getVal($attr) as $id) {
                    $view[]=[
                                V_TYPE=>V_ELEM,V_ATTR => $attr,
                                V_PROP => V_P_VAL,V_ID=>$id];
                }
            }
            if ($viewState == V_S_REF or $viewState == V_S_CREF) {
                $spec = array_merge($spec, $view);
            } else {
                $spec[$i]=[V_TYPE=>V_LIST,V_ARG=>$view];
                $i++;
            }

        }
        if ($viewState == V_S_REF or $viewState == V_S_CREF) {
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
        if ($viewState == V_S_CREA 
        or  $viewState == V_S_DELT 
        or  $viewState == V_S_UPDT) {
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

