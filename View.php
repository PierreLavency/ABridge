<?php

require_once("Model.php"); 
require_once("ViewConstant.php");
require_once("GenHTML.php");

define('V_TYPE', "v_type");
define('V_ELEM', "v_elem");
define('V_LIST', "v_list");
define('V_CREF', "v_cref");
define('V_CVAL', "v_cval");
define('V_REF', "v_ref");
define('V_ARG', "v_arg");
define('V_OBJ', "v_obj");
define('V_NAV', "v_nav");
define('V_NAVC', "new");
define('V_CNAV', "v_cnav");
define('V_ERROR', "v_error"); 
define('V_FORM', "v_form");


define('V_LT', "v_lt"); // list type
define('V_ALIST', "v_alist");
define('V_CLIST', "v_clist");

define('V_ID', "v_id");
define('V_STRING', "v_string");

// V_ELEM
define('V_ATTR', "v_attr");
    
define('V_PROP', "v_prop");   
define('V_P_INP', "v_p_Attr");
define('V_P_LBL', "v_p_Lbl");
define('V_P_VAL', "v_p_Val");
define('V_P_NAME', "v_p_Name");
define('V_P_TYPE', "v_p_type");
define('V_P_REF', "v_p_ref");

define('V_S_CREA', "Create"); // object view types
define('V_S_UPDT', "Update"); 
define('V_S_READ', "Read"); 
define('V_S_DELT', "Delete");
define('V_S_SLCT', "Select");
define('V_S_REF', "reference"); // reference view
define('V_S_CREF', "creference"); // collection view
define('V_B_SUBM', "Submit"); // buttons
define('V_B_CANC', "Cancel");
define('V_B_RFCH', "Refresh");


class View
{
    // property

    protected $_model;
    protected $_cmodel;
    protected $_path;

    protected $_attrHtml=['Sexe'=>H_T_RADIO,'A'=>H_T_SELECT,'De'=>H_T_SELECT]; //bof

    protected $_attrList;
    
    protected $_listHtml = [
                V_OBJ   => H_T_LIST_BR,
                V_CNAV  => H_T_1TABLE,
                V_NAV   => H_T_1TABLE,
                V_ALIST => H_T_TABLE,
                V_ATTR  => H_T_LIST,
                V_CLIST => H_T_LIST_BR,
                V_CREF  => H_T_LIST_BR,
                V_CVAL  => H_T_TABLE,
                V_S_REF => H_T_CONCAT, // do not change
                V_S_CREF=> H_T_LIST, // caller or callee ?
                V_ERROR => H_T_LIST,
                ];

    protected $_nav=[
              V_S_READ =>[
                [V_TYPE=>V_NAV,V_P_VAL=>V_S_UPDT],
                [V_TYPE=>V_NAV,V_P_VAL=>V_S_DELT],
                [V_TYPE=>V_NAV,V_P_VAL=>V_S_CREA],
                [V_TYPE=>V_NAV,V_P_VAL=>V_S_SLCT],
                ],
              V_S_SLCT =>[
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_RFCH],
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_CANC],
                [V_TYPE=>V_NAV,V_P_VAL=>V_S_CREA],
                ],
              V_S_CREA =>[
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_CANC],
                ],
              V_S_UPDT =>[
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_CANC],
                ],
              V_S_DELT =>[
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_NAV,V_P_VAL=>V_B_CANC],
                ],              
              ];

    protected $_navClass=[];

    protected $_attrProp=[
              V_S_READ =>[V_P_LBL,V_P_VAL],
              V_S_SLCT =>[V_P_LBL,V_P_VAL],
              V_S_CREA =>[V_P_LBL,V_P_VAL],
              V_S_UPDT =>[V_P_LBL,V_P_VAL],
              V_S_DELT =>[V_P_LBL,V_P_VAL],
              V_S_REF  =>[V_P_VAL],
              V_S_CREF =>[V_P_VAL],
              ];
              
    protected $_attrLbl = [];

    // constructors

    function __construct($model) 
    {
        $this->_model=$model; 
        $this->_cmodel=null;
    }

    // methods
    
    public function setAttrList($dspec,$viewState) 
    {
        $this->_attrList[$viewState]= $dspec;
        return true;
    }

    public function getAttrList($viewState)
    {
        if (isset($this->_attrList[$viewState])) {
            return $this->_attrList[$viewState];
        }
        $dspec = [];
        switch ($viewState) {
            case V_S_REF :
                $dspec = ['id']; 
                return $dspec;
            case V_S_SLCT :
                $res = array_diff(
                    $this->_model->getAllAttr(),
                    ['vnum','ctstp','utstp']
                );
                foreach ($res as $attr) {
                    $atyp=$this->_model->getTyp($attr);
                    if ($atyp != M_CREF and $atyp != M_TXT) {
                        $dspec[]=$attr;
                    }
                }
                return $dspec ;   
            case V_S_CREF :
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
                    $atyp=$this->_model->getTyp($attr);
                    if ($atyp != M_CREF and $atyp != M_TXT) {
                        $dspec[]=$attr;
                    }
                }
                return $dspec ;   
            default : 
                $dspec = array_diff(
                    $this->_model->getAllAttr(),
                    ['vnum','ctstp','utstp']
                );
                return $dspec;
        }
    }
    
    public function setListHtml($dspec) 
    {
        $this->_listHtml= $dspec;
        return true;
    }

    public function getListHtml($listType)
    {       
        if (isset($this->_listHtml[$listType])) {
            return $this->_listHtml[$listType];
        }
        return H_T_LIST;
    }
     
    public function setPropList($dspec,$viewState) 
    {
        $this->_attrProp[$viewState]= $dspec;
        return true;
    }

    public function getPropList($viewState) 
    {
        if (isset($this->_attrProp[$viewState])) {
            return $this->_attrProp[$viewState];
        }
        $res = [V_P_LBL,V_P_VAL];
        return $res;
    }

    public function setNavClass($dspec)
    {
        $this->_navClass=[];
        foreach ($dspec as $classN) {
            $this->_navClass[]= 
            [V_TYPE=>V_CNAV,V_OBJ=>$classN,V_P_VAL=>V_S_SLCT];
        }
        return true;
    }

    public function getNavClass($viewState)
    {
        if (isset($this->_navClass)) {
            return $this->_navClass;
        }
 
        return [];
    }
    
    public function setNav($dspec,$viewState) 
    {
        $this->_nav[$viewState]= $dspec;
        return true;
    }
    
    public function getNav($viewState) 
    {
        if (isset($this->_nav[$viewState])) {
            return $this->_nav[$viewState];
        }
        return [];
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
    
    public function setAttrListHtml($dspec) 
    {
        $this->_attrHtml= $dspec;
        return true;
    }
    
    public function getAttrHtml($attr) 
    {
        if (isset($this->_attrHtml[$attr])) {
            return $this->_attrHtml[$attr];
        }
        $typ = $this->_model->getTyp($attr);
        $res=H_T_TEXT;
        if ($typ == M_TXT) {
            $res=H_T_TEXTAREA;
        }
        if ($typ == M_CODE) {
            $res=H_T_SELECT;
        }
        return $res;
    }
    
    public function evale($spec,$viewState) 
    {
        $attr = $spec[V_ATTR];
        if ($attr == V_S_SLCT) { //bof
            $typ = M_CREF;
        } else {
            $typ = $this->_model->getTyp($attr);
        }
        $prop = $spec[V_PROP];
        $res = [];
        if (($viewState == V_S_CREA or $viewState == V_S_UPDT
            or $viewState == V_S_SLCT) 
            and $prop==V_P_VAL 
            and (! $this->_model->isProtected($attr)) 
            and
            ($this->_model->isMdtr($attr) or $this->_model->isOptl($attr))) {
            $res[H_NAME]=$attr;
            $default=null;
            if (isset($_POST[$attr])) {
                $default= $_POST[$attr];
            } else {
                if ($viewState == V_S_UPDT) {
                    $default=$this->_model->getVal($attr);
                }
                if ($viewState == V_S_CREA ) {
                    $default=$this->_model->getDflt($attr);
                }                  
            }
            if ($default) {
                $res[H_DEFAULT]=$default;
            }
            $htyp = $this->getAttrHtml($attr);
            $res[H_TYPE]=$htyp;
            if ($htyp == H_T_SELECT or $htyp == H_T_RADIO) {
                $vals=$this->_model->getValues($attr);
                $values=[];
                if ($htyp == H_T_SELECT 
                and (!$this->_model->isMdtr($attr))) {
                    $values[] = ["",""];
                }
                foreach ($vals as $v) {
                    if ($typ == M_CODE) {
                        $m = $this->_model->getCode($attr, (int) $v);
                    }
                    if ($typ == M_REF) {
                        $rmod = $this->_model->getRefMod($attr);
                        $m = new Model($rmod, (int) $v);
                    }
                    $vw = new View($m);
                    $l = $vw->show($this->_path, V_S_REF, false);
                    $r = [$v,$l];
                    $values[]=$r;
                }
                $res[H_VALUES]=$values;    
            }
            return $res ;
        }
        if ($viewState == V_S_CREF) {
            if ($typ == M_REF) {
                $rid = $this->_model->getVal($attr);
                $rmod = $this->_model->getRefMod($attr);
                if (($this->_cmodel->getId() == $rid) and 
                ($this->_cmodel->getModName() == $rmod)) {
                    return false;
                }
            }
        }
        if ($typ != M_CREF or $prop != V_P_VAL) {
            $x=$this->getProp($attr, $prop);
        }
        if ($prop==V_P_VAL) {
            if ($attr == 'id' and $viewState == V_S_CREF) {
                $res[H_TYPE]=H_T_LINK;
                $res[H_LABEL]=$this->show($this->_path, V_S_REF, false);
                $res[H_NAME]=$this->_path->getPath();
                return $res;
            }
            if ($typ==M_CREF and $attr != V_S_SLCT ) {     
                $id=$spec[V_ID];
                $m = $this->_model->getCref($attr, $id);
                $v = new View($m);
                $m = $this->_cmodel;
                if (is_null($m)) {
                    $m = $this->_model;
                }
                $this->_path->push($attr, $id);
                $v->_path=$this->_path;
                $res = $v->buildCView($m, V_S_CREF);
                $this->_path->pop();
                return $res; 
            }
            if ($typ==M_CREF and $attr == V_S_SLCT ) {     
                $id=$spec[V_ID];
                $m = new Model($this->_model->getModName(), $id);
                $v = new View($m);
                $m = $this->_cmodel;
                if (is_null($m)) {
                    $m = $this->_model;
                }
                $this->_path->pushid($id);
                $v->_path=$this->_path;
                $res = $v->buildCView($m, V_S_CREF);
                $this->_path->popid();
                return $res; 
            }
            if ($typ==M_REF) {
                $res[H_TYPE]=H_T_LINK;
                $m=$this->_model->getRef($attr);
                if (is_null($m)) {
                    return false;
                }
                $v = new View($m);
                $res[H_LABEL]=$v->show($this->_path, V_S_REF, false);
                $res[H_NAME]=$this->_path->getRefPath($m); 
                return $res;        
            }
            if ($typ==M_CODE and (!is_null($x))) {
                $m=$this->_model->getCode($attr, (int) $x);
                $v = new View($m);
                $x = $v->show($this->_path, V_S_REF, false);
            }
        }
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
        if ($typ ==  M_TXT and $prop==V_P_VAL) {
            $res = [H_TYPE =>H_T_TEXTAREA, H_DISABLED=>true, H_DEFAULT=>$x];
        }
        return $res;
    }
    
    public function evalo($dspec,$viewState) 
    {
        $name = $this->_model->getModName();
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$name];
        return $res;
    }
    
    public function evaln($nav,$viewState) 
    {
        $res=[];
        $nav=$nav[V_P_VAL];
        $res[H_TYPE]=H_T_LINK;
        $res[H_LABEL]=$this->getLbl($nav);
        if ($nav == V_S_CREA) {
            $path = $this->_path->getCreaPath();
        }
        if ($nav == V_S_SLCT) {
            $path = "'".$this->_path->getCreaPath().'?View='.$nav."'";
        }
        if ($nav == V_S_UPDT OR $nav == V_S_DELT) {
            $path = $this->_path->getPath(); 
            $path = "'".$path.'?View='.$nav."'";
        }
        if ($nav == V_B_CANC) {
            $path = $this->_path->getObjPath();
        }
        if ($nav == V_B_RFCH) {
            $path = "'".$this->_path->getCreaPath().'?View='.V_S_SLCT."'";
        }
        
        if ($nav == V_B_SUBM) {
            $res[H_TYPE]=H_T_SUBMIT;
            $res[H_LABEL]=$this->getLbl($viewState);
        } else {
            $res[H_NAME]=$path; 
        }
        return $res;
    }
    
    public function evalc($spec, $viewState)
    {
        $res=[];
        $nav=$spec[V_P_VAL];
        $mod=$spec[V_OBJ];
        $res[H_TYPE]=H_T_LINK;
        $res[H_LABEL]=$this->getLbl($mod);
        $path = "'".$this->_path->getClassPath($mod).'?View='.$nav."'";
        $res[H_NAME]=$path; 
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
            case V_NAV:
                    $result= $this->evaln($spec, $viewState);
                break;
            case V_CNAV:
                    $result= $this->evalc($spec, $viewState);
                break;
            case V_OBJ:
                    $result= $this->evalo($spec, $viewState);
                break;
            case V_LIST:
                    $lt = "";
                    if (isset($spec[V_LT])) {
                        $lt=$spec[V_LT];
                    }
                    $result[H_TYPE]=$this->getListHtml($lt);
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
                    $path = $this->_path->getPath();
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
            case V_NAVC:
                    $result[H_TYPE]=H_T_LINK;
                    $result[H_LABEL]=$this->getLbl(V_NAVC);
                    $path = $this->_path->getPath();
                    $result[H_NAME]="'".$path.'/'.$spec[V_ATTR]."'";// here
                break;
            case V_ERROR:
                    $result[H_TYPE]=H_T_PLAIN;
                    $result[H_DEFAULT]=$spec[V_STRING];
                break;                              
        }
        return $result;
    }

    public function show($path,$viewState,$show = true) 
    {
        $this->_path=$path;
        $r = $this->buildView($viewState);
        if ($viewState != V_S_REF and $viewState != V_S_CREF) {
            $r=genHTML($r, $show);
        } else {
            $r=genFormElem($r, $show);
        }
        return $r;
    }
    
    public function buildCView($cmodel,$viewState) 
    {
        $this->_cmodel = $cmodel;
        return ($this->buildView($viewState));
    }
    
    
    public function buildView($viewState) 
    {
        $spec=[];       
        $specL=[];  
        $specS=[];
        $arg = [];
        
        $labels = 
        [ 'Person'  => ['SurName','Name'],
          'Student' => ['SurName','Name'],
          'Cours'   => ['Name'],
          'CodeValue'=>['Name']];

        if (is_null($this->_model)) {
            $navClass= $this->getNavClass($viewState);
            $arg[]= [V_TYPE=>V_LIST,V_LT=>V_CNAV,V_ARG=>$navClass];
            $speci = [V_TYPE=>V_LIST,V_LT=>V_OBJ,V_ARG=>$arg];  
            $r=$this->subst($speci, $viewState);
            return $r;          
        }  
                  
        if (isset($labels[$this->_model->getModName()])) { // bof
            $x = $labels[$this->_model->getModName()];
            $this->setAttrList($x, V_S_REF);
        }
        
        
        foreach ($this->getAttrList($viewState) as $attr) {
            $view =[];
            $typ= $this->_model->getTyp($attr);
            if ($typ != M_CREF) {
                foreach ($this->getPropList($viewState) as $prop) {
                    $view[] = [V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => $prop];
                }
                if ($viewState == V_S_REF or $viewState == V_S_CREF) {
                    $spec = array_merge($spec, $view);
                } else {
                    $spec[]=[V_TYPE=>V_LIST,V_LT=>V_ATTR,V_ARG=>$view];
                }
            }
            if ($typ == M_CREF) {
                $view[]=[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_LBL];
                $view[]=[V_TYPE=>V_NAVC,V_ATTR => $attr];
                $viewe=[];
                foreach ($this->_model->getVal($attr) as $id) {
                    $viewe[]=[V_TYPE=>V_ELEM,V_ATTR => $attr,
                             V_PROP => V_P_VAL,V_ID=>$id];
                }
                $view[]=[V_TYPE=>V_LIST,V_LT=>V_CVAL,V_ARG=>$viewe];
                $specL[]=[V_TYPE=>V_LIST,V_LT=>V_CREF,V_ARG=>$view];
            }
        }
        if ($viewState == V_S_REF or $viewState == V_S_CREF) {
            $specf = [V_TYPE=>V_LIST,V_LT=>$viewState,V_ARG=>$spec];
            $r=$this->subst($specf, $viewState);
            return $r;
        }
        $arg = [];
        $navClass= $this->getNavClass($viewState);
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_CNAV,V_ARG=>$navClass]; 
        $arg[]= [V_TYPE=>V_OBJ];
        $navs = $this->getNav($viewState);
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_NAV,V_ARG=>$navs];
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_ALIST,V_ARG=>$spec];
        if ($viewState == V_S_SLCT ) {
            $view=[];
            $view[]=[V_TYPE=>V_ELEM,V_ATTR => V_S_SLCT, V_PROP => V_P_LBL];
            $viewe=[];
            foreach ($this->_model->select() as $id) {
                $viewe[]=[V_TYPE=>V_ELEM,V_ATTR => V_S_SLCT,
                          V_PROP => V_P_VAL,V_ID=>$id];
            }
            $view[]=[V_TYPE=>V_LIST,V_LT=>V_CVAL,V_ARG=>$viewe];
            $specS[]=[V_TYPE=>V_LIST,V_LT=>V_CREF,V_ARG=>$view];
            $arg[] = [V_TYPE=>V_LIST,V_LT=>V_CLIST,V_ARG=>$specS];
        }
        if ($this->_model->isErr()) {
            $e = $this->viewErr();
            $arg[]=$e;
        }
        if ($viewState == V_S_CREA 
        or  $viewState == V_S_DELT 
        or  $viewState == V_S_UPDT
        or  $viewState == V_S_SLCT) {
            $speci = [V_TYPE=>V_FORM,V_LT=>V_OBJ,V_ARG=>$arg];
            $r=$this->subst($speci, $viewState);
            return $r;
        }
        $arg[] = [V_TYPE=>V_LIST,V_LT=>V_CLIST,V_ARG=>$specL];
        $speci = [V_TYPE=>V_LIST,V_LT=>V_OBJ,V_ARG=>$arg];  
        $r=$this->subst($speci, $viewState);
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
            $result = [V_TYPE =>V_LIST,V_LT=>V_ERROR,V_ARG=>$result];
            return $result;
    }

};

