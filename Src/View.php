
<?php

require_once 'Handle.php'; 
require_once 'GenHTML.php';
require_once 'CstView.php';
require_once 'CstMode.php';

class View
{
    // property

    protected $_handle;
    protected $_req;
    
    protected $_name;

    protected $_attrHtml= [];
                
    protected $_attrList;
    
    protected $_listHtml = [
                V_VLIST => H_T_1TABLE,
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
    protected $_navView=[];
    
    protected $_attrProp=[
              V_S_READ =>[V_P_LBL,V_P_VAL],
              V_S_SLCT =>[V_P_LBL,V_P_OP,V_P_VAL],
              V_S_CREA =>[V_P_LBL,V_P_VAL],
              V_S_UPDT =>[V_P_LBL,V_P_VAL],
              V_S_DELT =>[V_P_LBL,V_P_VAL],
              V_S_REF  =>[V_P_VAL],
              V_S_CREF =>[V_P_VAL],
              ];
              
    protected $_attrLbl = [];

    // constructors

    function __construct($handle) 
    {
        $this->_handle=$handle;
        $this->_req=$handle->getReq();
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
                    $this->_handle->getAttrList(),
                    ['vnum','ctstp','utstp']
                );
                foreach ($res as $attr) {
                    $atyp=$this->_handle->getTyp($attr);
                    $eval = $this->_handle->isEval($attr);
                    if ($atyp != M_CREF and $atyp != M_TXT and !$eval) {
                        $dspec[]=$attr;
                    }
                }
                return $dspec ;   
            case V_S_CREF :
                $res = array_diff(
                    $this->_handle->getAttrList(),
                    ['vnum','ctstp','utstp']
                );
                $ref = $this->getAttrList(V_S_REF);
                $key = array_search('id', $ref);
                if ($key!==false) {
                    unset($ref[$key]);
                } 
                $res = array_diff($res, $ref);  
                foreach ($res as $attr) {
                    $atyp=$this->_handle->getTyp($attr);
                    if ($atyp != M_CREF and $atyp != M_TXT) {
                        $dspec[]=$attr;
                    }
                }
                return $dspec ;   
            default : 
                $dspec = array_diff(
                    $this->_handle->getAttrList(),
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

    
    public function setNavView($dspec,$viewState)
    {
        $this->_navView=[];
        foreach ($dspec as $viewN) {
            $this->_navView[]= 
            [V_TYPE=>V_VNAV,V_P_VAL=>$viewN];
        }
        return true;
    }
    
    public function getNavView($viewState)
    {
        if (isset($this->_navView)) {
            return $this->_navView;
        }
        return [];
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
        $navList=[];
        foreach ($dspec as $navE) {
            $navList[]= [V_TYPE=>V_NAV,V_P_VAL=>$navE];
        }
        $this->_nav[$viewState]= $navList;
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
                $val = $this->_handle->getVal($attr);
                return $val;
                break;
            case V_P_TYPE:  
                return $this->_handle->getTyp($attr);
                break;
            case V_P_NAME:
                return $attr;
                break;
            case V_P_OP:
                return ':';
                break;              
            default:
                return 0;
        }       
    }
    
    public function setAttrListHtml($dspec,$viewState) 
    {
        $this->_attrHtml[$viewState]= $dspec;
        return true;
    }
 

    public function getAttrHtml($attr,$viewState) 
    {
        if (isset($this->_attrHtml[$viewState])) {
            $list = $this->_attrHtml[$viewState];
            if (isset($list[$attr])) {
                return $list[$attr];
            }
        }
        
        if ($attr == V_S_SLCT) { //bof
            $typ = M_CREF;
        } else {
            $typ = $this->_handle->getTyp($attr);
        }
        if ($typ == M_REF) {
            return V_S_REF;
        }
        if ($typ==M_TXT) {
           return H_T_TEXTAREA;
        }
        if ($typ == M_CREF) {
            return [H_SLICE=>10,];
        }
        return H_T_PLAIN;
    }
    
    public function getUpAttrHtml($attr,$viewState) 
    {
        if (isset($this->_attrHtml[$viewState])) {
            $list = $this->_attrHtml[$viewState];
            if (isset($list[$attr])) {
                return $list[$attr];
            }
        }
        $typ = $this->_handle->getTyp($attr);
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
            $typ = $this->_handle->getTyp($attr);
        }
        $prop = $spec[V_PROP];
        $res = [];
        
        if ($prop == V_P_OP and $viewState == V_S_SLCT) {
            if ($this->_handle->isProtected($attr)) {
                $res[H_TYPE]=H_T_PLAIN;
                $res[H_DEFAULT]= '';
                return $res;
            }
            $res[H_TYPE]=H_T_SELECT;
            $name=$attr.'_OP';
            $res[H_NAME]=$name;
            $res[H_VALUES]=[['=','='],['>','>'],['<','<']];
            if ($typ == M_CODE) {
                $res[H_VALUES]=[['=','=']];
            }
            if ($typ == M_STRING) {
                $res[H_VALUES]=[['::','::'],['=','='],['>','>'],['<','<']];
            }
            $default=$this->_req->getPrm($name);
            if (is_null($default)) {
                $default= '=';
            }
            $res[H_DEFAULT]=$default;
            return $res;
        }
        
        if ($prop==V_P_VAL and 
            ((($viewState == V_S_CREA or $viewState == V_S_UPDT)
             and $this->_handle->isModif($attr))
            or 
            ($viewState == V_S_SLCT 
             and $this->_handle->isSelect($attr)))) {
            $res[H_NAME]=$attr;
            $default=$this->_req->getPrm($attr);
            if (is_null($default)) {
                    if ($viewState == V_S_UPDT) {
                        $default=$this->_handle->getVal($attr);
                    }
                    if ($viewState == V_S_CREA ) {
                        $default=$this->_handle->getDflt($attr);
                    }                  
            }
            $res[H_DEFAULT]=$default;   
            $htyp = $this->getUpAttrHtml($attr, $viewState);
            $res[H_TYPE]=$htyp;
            if ($htyp == H_T_SELECT or $htyp == H_T_RADIO) {
                $vals=$this->_handle->getValues($attr);
                $values=[];
                if ($htyp == H_T_SELECT 
                and ((!$this->_handle->isMdtr($attr)) 
                or $viewState == V_S_SLCT)) {
                    $values[] = ["",""];
                }
                foreach ($vals as $v) {
                    $m = $this->_handle->getCode($attr, (int) $v);
                    $vw = new View($m);
                    $l = $vw->show(V_S_REF, false);
                    $r = [$v,$l];
                    $values[]=$r;
                }
                $res[H_VALUES]=$values;    
            }
            return $res ;
        }
        if ($viewState == V_S_CREF and $typ == M_REF 
        and $this->_handle->isMainRef($attr)
        ) {
            return false;
        }
        
        if ($typ != M_CREF or $prop != V_P_VAL) {
            $x=$this->getProp($attr, $prop);
        }
        if ($prop==V_P_VAL) {
            if ($attr == 'id' and $viewState == V_S_CREF) {
                $res[H_TYPE]=H_T_LINK;
                $res[H_LABEL]=$this->show(V_S_REF, false);
                $res[H_NAME]=$this->_handle->getPath();
                return $res;
            }
            if ($typ==M_CREF) {     
                $id=$spec[V_ID];
                if ($attr != V_S_SLCT) {
                    $nh=$this->_handle->getCref($attr, $id);
                } else {
                    $nh=$this->_handle->getObjId($id);
                }
                $v = new View($nh);
                $res = $v->buildView(V_S_CREF, true);
                return $res; 
            }
            if ($typ==M_REF) {
                $nh=$this->_handle->getRef($attr);
                if (is_null($nh)) {
                    return [H_TYPE =>H_T_PLAIN, H_DEFAULT=>""];
                }
                if ($this->_handle->isProtected($attr)) {
                    $rep=V_S_REF;
                } else {
                    $rep= $this->getAttrHtml($attr, $viewState);
                }
                if (is_array($rep)) {
                    $res=$rep;
                } else {
                    $res[H_TYPE]= $rep;
                }
                if ($rep==V_S_REF) {
                    $refpath = $nh->getPath();                
                    $res[H_TYPE]= H_T_LINK;
                    $res[H_NAME]=$refpath; 
                    if (is_null($refpath)) {
                        $res[H_TYPE]= H_T_PLAIN;
                    }
                }
                if ($rep == H_T_PLAIN) {
                    $rep=V_S_REF;
                }
                if ($rep != V_S_REF and $rep != H_T_PLAIN) {
                    $res[H_TYPE] = H_T_PLAIN;
                }
                $v = new View($nh);
                $res[H_LABEL]=$v->showRec($rep);
                $res[H_DEFAULT]=$res[H_LABEL];
                return $res;        
            }
            if ($typ==M_CODE and (!is_null($x))) {
                $nh=$this->_handle->getCode($attr, (int) $x);
                $v = new View($nh);
                $x = $v->show(V_S_REF, false);
            }
        }
        if ($prop==V_P_VAL) {
            $htyp = $this->getAttrHtml($attr, $viewState);
            if (is_array($htyp)) {
                $res= $htyp;
            } else {
                $res[H_TYPE] = $htyp;
            }
            if ($res[H_TYPE]==H_T_LINK) {
                $res[H_NAME]=$x;
                $res[H_LABEL]=$x;
            } else {
                $res[H_DEFAULT]=$x;
                $res[H_DISABLED]=true;
            }
            if ($res[H_TYPE]==H_T_IMG) {
                $tres=$res;
                $res=[];
                $res[H_TYPE]=H_T_LINK;
                $res[H_NAME]=$x;
                $res[H_LABEL]=$tres;
            }
            return $res;
        }
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
        return $res;
    }
    
    public function evalv($spec,$viewState) 
    {
        $viewn=$spec[V_P_VAL];
        $res= [];
        if ($viewState != V_S_READ) {
            return false;
        }       
        if ($viewn != $this->_name) {
            $res[H_LABEL]=$this->getLbl($viewn);
            $res[H_NAME]="'".$this->_handle->getPath().'?View='.$viewn."'";
            $res[H_TYPE]=H_T_LINK;
        } else {
            $res[H_TYPE]=H_T_PLAIN;
            $res[H_DEFAULT]=$this->getLbl($viewn);
        }
        return $res;
    }
    
    public function evalo($dspec,$viewState) 
    {
        $name = $this->_handle->getModName();
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$name];
        return $res;
    }   

    public function evaln($nav,$viewState) 
    {
        $res=[];
        $nav=$nav[V_P_VAL];
        if ($nav == V_B_SUBM) {
            $res[H_TYPE]=H_T_SUBMIT;
            $res[H_LABEL]=$this->getLbl($viewState);
        } else {
            $con='&';
            $res[H_TYPE]=H_T_LINK;
            $res[H_LABEL]=$this->getLbl($nav);
            if ($nav==V_B_CANC) {
                $nav=V_S_READ;
                $con='?';
            }
            if ($nav==V_B_RFCH) {
                $nav=V_S_SLCT;
            }
            if (!$this->_handle->isAllowed($nav)) {
                return false;
            }
            $path = $this->_req->getActionPath($nav);
            if (!is_null($this->_name)) {
                $path = $path.$con.'View='.$this->_name;
            }
            $res[H_NAME]="'".$path."'"; 
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
        if ($mod == 'Home') {
            $path = $this->_req->getHomePath();
        } else {
            if (!$this->_handle->isAllowedMod($mod, $nav)) {
                return false;
            }
            $path = $this->_req->getClassPath($mod, $nav);
        } 
        $res[H_NAME]="'".$path."'";
        return $res;
    }
    
    public function evalcn($spec,$viewState)
    {
        $result=[];
        $nav=$spec[V_P_VAL];
        $result[H_LABEL]=$this->getLbl($nav);
        $attr=$spec[V_ATTR];
        if ($nav==V_B_NEW) {
            $result[H_TYPE]=H_T_LINK;
            if (!$this->_handle->isAllowedCref($attr, V_S_CREA)) {
                return false;
            }           
            $path="'".$this->_req->getCrefPath($attr, V_S_CREA)."'";
            $result[H_NAME]=$path;
        } else {
            $pos = $spec[V_ID];
            $viewn="";
            if (!is_null($this->_name)) {
                $viewn='&View='.$this->_name;
            }
            $path="'".$this->_handle->getPath().'?'.$attr.'='.$pos.$viewn."'";
            if ($viewState == V_S_SLCT) {
                $result[H_TYPE]=H_T_SUBMIT;
                $result[H_BACTION]=$path;   
            }
            if ($viewState == V_S_READ) {
                $result[H_TYPE]=H_T_LINK;;
                $result[H_NAME]=$path;  
            }
        }
        return $result;
    }
    
    public function subst($spec,$viewState)
    {
        $type = $spec[V_TYPE];
        $result=[];
        switch ($type) {
            case V_ELEM:
                    $result= $this->evale($spec, $viewState);
                break;
            case V_VNAV:
                    $result= $this->evalv($spec, $viewState);
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
                    $path = $this->_handle->getPath();
                    $result[H_ACTION]="POST";
                    $hid = [];
                    $hid['Action']=$viewState;
                    if (!is_null($this->_name)) {
                        $hid['View']=$this->_name;
                    }   
                    $result[H_HIDDEN]=$hid;
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
                    $result= $this->evalcn($spec, $viewState);
                break;
            case V_PLAIN:
            case V_ERROR:
                    $result[H_TYPE]=H_T_PLAIN;
                    $result[H_DEFAULT]=$spec[V_STRING];
                break;                              
        }
        return $result;
    }

    public function show($viewState,$show = true) 
    {
        $r = $this->buildView($viewState, false);
        if ($viewState != V_S_REF and $viewState != V_S_CREF) {
            $r=genHTML($r, $show);
        } else {
            $r=genFormElem($r, $show);
        }
        return $r;
    }
  
    protected function showRec($name) 
    {
        if ($name != V_S_REF and $name != V_S_CREF) {
            $this->_name=$name;
            $viewState=V_S_READ;
        } else {
            $viewState=$name;
        }
        $r = $this->buildView($viewState, true);
        $r=genFormElem($r, false);
        return $r;
    }
  
    public function initView($handle,$viewState,$rec)
    {
        if ($handle->nullObj()) {
            return true;
        }
        if (!$rec) {
            $this->_name=$this->_req->getPrm('View');
        }
        $modName = $handle->getModName();
        $spec = Handler::get()->getViewHandler($modName);
        if (is_null($spec)) {
                return true;
        }
        $this->setView($spec, $viewState);       
        if ($viewState == V_S_CREF) {
            $this->initView($handle, V_S_REF, true);
        }
    }
 
    public function setView($spec,$viewState)
    {       
        if (isset($spec['attrList'])) {
            $specma=$spec['attrList'];
            if (isset($specma[$viewState])) {
                $this->setAttrList($specma[$viewState], $viewState);
            }
        }
        if (isset($spec['attrHtml'])) {
            $specma=$spec['attrHtml'];
            if (isset($specma[$viewState])) {
                $this->setAttrListHtml($specma[$viewState], $viewState);
            }
        }
        if (isset($spec['attrProp'])) {
            $specma=$spec['attrProp'];
            if (isset($specma[$viewState])) {
                $this->setPropList($specma[$viewState], $viewState);
            }
        }
        if (isset($spec['navList'])) {
            $specma=$spec['navList'];
            if (isset($specma[$viewState])) {
                $this->setNav($specma[$viewState], $viewState);
            }
        }
        if (isset($spec['lblList'])) {
            $specma=$spec['lblList'];
            $this->setLblList($specma);
        }
        if ($viewState !=  V_S_REF and $viewState !=  V_S_CREF) {     
            if (isset($spec['viewList'])) {
                $specma=$spec['viewList'];
                $viewL=[];
                $first = true;
                foreach ($specma as $viewN=>$vspec) {
                    $viewL[]=$viewN;
                    if (is_null($this->_name) and $first) {
                        $this->_name=$viewN;
                    }
                    if ($this->_name==$viewN) {
                        $this->setView($vspec, $viewState);
                    }
                    $first=false;
                }
                $this->setNavView($viewL, $viewState);
            }
        }
    }
    
    public function buildView($viewState,$rec) 
    {
        $spec=[];       
        $specL=[];  
        $specS=[];
        $arg = [];
        
        if (!is_null($this->_handle)) {
            $this->initView($this->_handle, $viewState, $rec);
        }
        if (is_null($this->_handle) or $this->_handle->nullObj()) {
            $navClass= $this->getNavClass($viewState);
            $arg[]= [V_TYPE=>V_LIST,V_LT=>V_CNAV,V_ARG=>$navClass];
            $speci = [V_TYPE=>V_LIST,V_LT=>V_OBJ,V_ARG=>$arg];  
            $r=$this->subst($speci, $viewState);
            return $r;          
        }               
        foreach ($this->getAttrList($viewState) as $attr) {
            $view =[];
            $typ= $this->_handle->getTyp($attr);
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
                $view[]=[V_TYPE=>V_NAVC,V_ATTR => $attr,V_P_VAL=>V_B_NEW];
                $list = $this->_handle->getVal($attr);
                $view = $this->getSlice($attr, $list, $view, $viewState);
                $specL[]=[V_TYPE=>V_LIST,V_LT=>V_CREF,V_ARG=>$view];
            }
        }
        if ($viewState == V_S_REF or $viewState == V_S_CREF) {
            $specf = [V_TYPE=>V_LIST,V_LT=>$viewState,V_ARG=>$spec];
            $r=$this->subst($specf, $viewState);
            return $r;
        }
        if ($rec) {
            $specf = [V_TYPE=>V_LIST,V_LT=>V_ALIST,V_ARG=>$spec];
            $r=$this->subst($specf, $viewState);
            return $r;
        }
        $arg = [];
        $navClass= $this->getNavClass($viewState);
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_CNAV,V_ARG=>$navClass];
        $navView[]=[V_TYPE=>V_OBJ];
        $navView = array_merge($navView, $this->getNavView($viewState));
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_VLIST,V_ARG=>$navView];
        $navs = $this->getNav($viewState);
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_NAV,V_ARG=>$navs];
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_ALIST,V_ARG=>$spec];
        if ($viewState == V_S_SLCT ) {
            $view=[];
            $view[]=[V_TYPE=>V_ELEM,V_ATTR => V_S_SLCT, V_PROP => V_P_LBL];
            $list=$this->_handle->select();
            $view = $this->getSlice(V_S_SLCT, $list, $view, $viewState);
            $specS[]=[V_TYPE=>V_LIST,V_LT=>V_CREF,V_ARG=>$view];
            $arg[] = [V_TYPE=>V_LIST,V_LT=>V_CLIST,V_ARG=>$specS];
        }
        if ($this->_handle->isErr()) {
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

    protected function getSlice($attr,$list,$viewL,$viewState) 
    {       
        $view[]=$viewL[0];
        $c=count($list);
        $pos=0;
        $prm = $this->getAttrHtml($attr, $viewState);
        $slice = $prm[H_SLICE];
        $npos = $this->_req->getPrm($attr); //not work
        if (!is_null($npos)) {
            $pos=(int) $npos;
            if ($pos<0) {
                $pos=-$pos-$slice;
                if ($pos<0) {
                    $pos=0;
                }
            }
            if ($pos > $c) {
                $pos=$c-$slice;
            }               
        }
        if ($c > $slice) {
                $list= array_slice($list, $pos, $slice);
        }
        $viewe=[];
        foreach ($list as $id) {
            $viewe[]=[V_TYPE=>V_ELEM,V_ATTR => $attr,
                      V_PROP => V_P_VAL,V_ID=>$id];
        }
        $ind = $c;
        if ($c > $slice) {
            $nc=count($list)+$pos;
            $ind=$c.' : '.$pos.'-'.$nc;
        }
        $view[]=[V_TYPE=>V_PLAIN,V_STRING=>$ind];
        if ($c > $slice) {
            $npos= $pos+$slice;
            if ($npos>=$c) {
                $npos=$pos;
            }
            $view[]=
            [V_TYPE=>V_NAVC,V_ATTR => $attr,V_P_VAL=>V_B_PRV,V_ID=>-$pos];
            $view[]=
            [V_TYPE=>V_NAVC,V_ATTR => $attr,V_P_VAL=>V_B_NXT,V_ID=>$npos];
        }
        $first = true;
        foreach ($viewL as $elm) {
            if (! $first) {
                $view[] = $elm;
            }
            $first = false;
        }
        $view[]=[V_TYPE=>V_LIST,V_LT=>V_CVAL,V_ARG=>$viewe];
        return $view;
    }
    
    public function viewErr() 
    {
            $log = $this->_handle->getErrLog();
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

