
<?php

require_once 'Handle.php';
require_once 'GenHTML.php';
require_once 'CstView.php';
require_once 'CstMode.php';

class View
{
    // property

    protected $handle;
    
    protected $name;

    protected $attrHtml= [];
                
    protected $attrList;
    
    protected $htmlList = [
                V_VLIST         => H_T_1TABLE,
                V_OBJ           => H_T_LIST_BR,
                V_TOPMENU       => H_T_1TABLE,
                V_OBJACTIONMENU => H_T_1TABLE,
                V_ALIST         => H_T_TABLE,
                V_ATTR          => H_T_LIST,
                V_CLIST         => H_T_LIST_BR,
                V_CREF          => H_T_LIST_BR,
                V_CREF_MLIST    => H_T_1TABLE,
                V_CVAL          => H_T_TABLE,
                V_S_REF         => H_T_CONCAT, // do not change
                V_S_CREF        => H_T_LIST_BR, // caller or callee ?
                V_ERROR         => H_T_LIST,
                ];

    protected $nav=[
              V_S_READ =>[
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_S_UPDT],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_S_DELT],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_S_CREA],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_S_SLCT],
                ],
              V_S_SLCT =>[
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_RFCH],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_CANC],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_S_CREA],
                ],
              V_S_CREA =>[
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_CANC],
                ],
              V_S_UPDT =>[
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_CANC],
                ],
              V_S_DELT =>[
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_SUBM],
                [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>V_B_CANC],
                ],
              ];

    protected $topMenu=[];
    protected $navView=[];
    
    protected $attrProp=[
              V_S_READ =>[V_P_LBL,V_P_VAL],
              V_S_SLCT =>[V_P_LBL,V_P_OP,V_P_VAL],
              V_S_CREA =>[V_P_LBL,V_P_VAL],
              V_S_UPDT =>[V_P_LBL,V_P_VAL],
              V_S_DELT =>[V_P_LBL,V_P_VAL],
              V_S_REF  =>[V_P_VAL],
              V_S_CREF =>[V_P_VAL],
              ];
              
    protected $attrLbl = [];

    // constructors

    public function __construct($handle)
    {
        $this->handle=$handle;
    }

    // methods
    
    public function setAttrList($dspec, $viewState)
    {
        $this->attrList[$viewState]= $dspec;
        return true;
    }

    public function getAttrList($viewState)
    {
        if (isset($this->attrList[$viewState])) {
            return $this->attrList[$viewState];
        }
        $dspec = [];
        switch ($viewState) {
            case V_S_REF:
                $dspec = ['id'];
                return $dspec;
            case V_S_SLCT:
                $res = array_diff(
                    $this->handle->getAttrList(),
                    ['vnum','ctstp','utstp']
                );
                foreach ($res as $attr) {
                    $atyp=$this->handle->getTyp($attr);
                    $eval = $this->handle->isEval($attr);
                    if ($atyp != M_CREF and isStruct($atyp) and !$eval) {
                        $dspec[]=$attr;
                    }
                }
                return $dspec;
            case V_S_CREF:
                $res = array_diff(
                    $this->handle->getAttrList(),
                    ['vnum','ctstp','utstp']
                );
                $ref = $this->getAttrList(V_S_REF);
                $key = array_search('id', $ref);
                if ($key!==false) {
                    unset($ref[$key]);
                }
                $res = array_diff($res, $ref);
                foreach ($res as $attr) {
                    $atyp=$this->handle->getTyp($attr);
                    if ($atyp != M_CREF and isStruct($atyp)) {
                        $dspec[]=$attr;
                    }
                }
                return $dspec;
            default:
                $dspec = array_diff(
                    $this->handle->getAttrList(),
                    ['vnum','ctstp','utstp']
                );
                return $dspec;
        }
    }
    
    public function setHtmlList($dspec)
    {
        $this->htmlList= $dspec;
        return true;
    }

    public function getHtmlList($listType, $viewState)
    {
        if (isset($this->htmlList[$listType])) {
            return $this->htmlList[$listType];
        }
        return H_T_LIST;
    }
     
    public function setPropList($dspec, $viewState)
    {
        $this->attrProp[$viewState]= $dspec;
        return true;
    }

    public function getPropList($viewState)
    {
        if (isset($this->attrProp[$viewState])) {
            return $this->attrProp[$viewState];
        }
        $res = [V_P_LBL,V_P_VAL];
        return $res;
    }

    public function setMenuObjView($dspec, $viewState)
    {
        $this->navView=[];
        foreach ($dspec as $viewN) {
            $this->navView[]=
            [V_TYPE=>V_OBJVIEWMENU,V_P_VAL=>$viewN];
        }
        return true;
    }
    
    public function getMenuObjView($viewState)
    {
        if (isset($this->navView)) {
            return $this->navView;
        }
        return [];
    }
    
    public function setTopMenu($dspec)
    {
        $this->topMenu=[];
        foreach ($dspec as $classN) {
            $action =  V_S_SLCT;
            if (is_array($classN)) {
                $path=$classN[0];
                $action = $classN[1];
            } else {
                $path=$classN;
            }
            $this->topMenu[]=
            [V_TYPE=>V_TOPMENU,V_OBJ=>$path,V_P_VAL=>$action];
        }
        return true;
    }

    public function getTopMenu($viewState)
    {
        if (isset($this->topMenu)) {
            return $this->topMenu;
        }
        return [];
    }
    
    public function setMenuObjAction($dspec, $viewState)
    {
        $navList=[];
        foreach ($dspec as $navE) {
            $navList[]= [V_TYPE=>V_OBJACTIONMENU,V_P_VAL=>$navE];
        }
        $this->nav[$viewState]= $navList;
        return true;
    }
    
    public function getMenuObjAction($viewState)
    {
        if (isset($this->nav[$viewState])) {
            return $this->nav[$viewState];
        }
        return [];
    }
 
    public function setLblList($dspec)
    {
        $this->attrLbl= $dspec;
        return true;
    }
    
    public function getLbl($attr)
    {
        foreach ($this->attrLbl as $x => $lbl) {
            if ($x==$attr) {
                return $lbl;
            }
        }
        return $attr;
    }
        
    public function getProp($attr, $prop)
    {
        switch ($prop) {
            case V_P_LBL:
                return $this->getLbl($attr);
                break;
            case V_P_VAL:
                $val = $this->handle->getVal($attr);
                return $val;
                break;
            case V_P_TYPE:
                return $this->handle->getTyp($attr);
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
    
    public function setAttrListHtml($dspec, $viewState)
    {
        $this->attrHtml[$viewState]= $dspec;
        return true;
    }
 

    public function getAttrHtml($attr, $viewState)
    {
        if (isset($this->attrHtml[$viewState])) {
            $list = $this->attrHtml[$viewState];
            if (isset($list[$attr])) {
                return $list[$attr];
            }
        }
        
        if ($attr == V_S_SLCT) { //bof
            $typ = M_CREF;
        } else {
            $typ = $this->handle->getTyp($attr);
        }
        if ($typ == M_REF) {
            return V_S_REF;
        }
        if ($typ == M_CREF) {
            if ($attr!= V_S_SLCT and $this->handle->isOneCref($attr)) {
                return [V_CTYP=>V_C_TYP1];
            }
            return [H_SLICE=>10,V_COUNTF=>true,V_CTYP=>V_C_TYPN,V_CVAL=>H_T_TABLE];
        }
        if ($typ == M_HTML) {
            return H_T_PLAIN;
        }
        if (! isStruct($typ)) {
            return H_T_TEXTAREA;
        }
        return H_T_PLAIN;
    }
    
    public function getUpAttrHtml($attr, $viewState)
    {
        if (isset($this->attrHtml[$viewState])) {
            $list = $this->attrHtml[$viewState];
            if (isset($list[$attr])) {
                return $list[$attr];
            }
        }
        $typ = $this->handle->getTyp($attr);
        $res=H_T_TEXT;
        if (!isStruct($typ)) {
            $res=H_T_TEXTAREA;
        }
        if ($typ == M_CODE) {
            $res=H_T_SELECT;
        }
        return $res;
    }
    
    public function element($spec, $viewState)
    {
        $attr = $spec[V_ATTR];
        if ($attr == V_S_SLCT) { //bof
            $typ = M_CREF;
        } else {
            $typ = $this->handle->getTyp($attr);
        }
        $prop = $spec[V_PROP];
        $res = [];
        
        if ($prop == V_P_OP and $viewState == V_S_SLCT) {
            if ($this->handle->isProtected($attr)) {
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
            $default=$this->handle->getPrm($name);
            if (is_null($default)) {
                $default= '=';
            }
            $res[H_DEFAULT]=$default;
            return $res;
        }
        
        if ($prop==V_P_VAL and
            ((($viewState == V_S_CREA or $viewState == V_S_UPDT)
             and $this->handle->isModif($attr))
            or
            ($viewState == V_S_SLCT
             and $this->handle->isSelect($attr)))) {
            $res[H_NAME]=$attr;
            $default=$this->handle->getPrm($attr, isRaw($typ));
            if (is_null($default)) {
                if ($viewState == V_S_UPDT) {
                    $default=$this->handle->getVal($attr);
                }
                if ($viewState == V_S_CREA) {
                    $default=$this->handle->getDflt($attr);
                }
            }
            $res[H_DEFAULT]=$default;
            $htyp = $this->getUpAttrHtml($attr, $viewState);
            if (is_array($htyp)) {
                foreach ($htyp as $elmT => $elmV) {
                    $res[$elmT]=$elmV;
                }
            } else {
                $res[H_TYPE]=$htyp;
            }
            if ($htyp == H_T_SELECT or $htyp == H_T_RADIO) {
                $vals=$this->handle->getValues($attr);
                $values=[];
                if ($htyp == H_T_SELECT
                and ((!$this->handle->isMdtr($attr))
                or $viewState == V_S_SLCT)) {
                    $values[] = ["",""];
                }
                foreach ($vals as $v) {
                    $m = $this->handle->getCode($attr, (int) $v);
                    if (! is_null($m)) {
                        $vw = new View($m);
                        $l = $vw->show(V_S_REF, false);
                        $r = [$v,$l];
                        $values[]=$r;
                    }
                }
                $res[H_VALUES]=$values;
            }
            return $res ;
        }
        if ($viewState == V_S_CREF and $typ == M_REF
        and $this->handle->isMainRef($attr)
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
                $res[H_NAME]=$this->handle->getUrl();
                return $res;
            }
            if ($typ==M_CREF) {
                $id=$spec[V_ID];
                if ($attr != V_S_SLCT) {
                    $nh=$this->handle->getCref($attr, $id);
                } else {
                    $nh=$this->handle->getObjId($id);
                }
                if (is_null($nh)) {
                    return false;
                }
                $v = new View($nh);
                $res = $v->buildView(V_S_CREF, true);
                return $res;
            }
            if ($typ==M_REF) {
                $nh=$this->handle->getRef($attr);
                if (is_null($nh)) {
                    return [H_TYPE =>H_T_PLAIN, H_DEFAULT=>""];
                }
                if ($this->handle->isProtected($attr)) {
                    $rep=V_S_REF;
                } else {
                    $rep=$this->getAttrHtml($attr, $viewState);
                }
                if (is_array($rep)) {
                    $res=$rep;
                } else {
                    $res[H_TYPE]= $rep;
                }
                if ($rep==V_S_REF) {
                    $refpath = $nh->getUrl();
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
                $nh=$this->handle->getCode($attr, (int) $x);
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
                $pict = 'C:\xampp\htdocs'.$x;
                if ($x) {
                    list($width, $height, $itype, $iattr) = getimagesize($pict);
                } else {
                    $width = 1;
                    $height = 1;
                }
                $res[H_NAME]=$x;
                $rf = 1;
                if (isset($tres[H_ROWP])) {
                    $rf= $tres[H_ROWP]/$height;
                }
                $cf = 1;
                if (isset($tres[H_COLP])) {
                    $cf = $tres[H_COLP]/$width;
                }
                if ($rf > $cf) {
                    $cf=$rf;
                } else {
                    $rf=$cf;
                }
                $tres[H_ROWP]= round($height * $rf);
                $tres[H_COLP]= round($width * $cf);
                $res[H_LABEL]=$tres;
            }
            return $res;
        }
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$x];
        return $res;
    }
    
    public function menuObjView($spec, $viewState)
    {
        $viewn=$spec[V_P_VAL];
        $res= [];
        if ($viewState != V_S_READ) {
            return false;
        }
        if ($viewn != $this->_name) {
            $res[H_LABEL]=$this->getLbl($viewn);
            $res[H_NAME]=$this->handle->getUrl(['View'=>$viewn]);
            $res[H_TYPE]=H_T_LINK;
        } else {
            $res[H_TYPE]=H_T_PLAIN;
            $res[H_DEFAULT]=$this->getLbl($viewn);
        }
        return $res;
    }

    public function menuObjAction($nav, $viewState)
    {
        $res=[];
        $nav=$nav[V_P_VAL];
        if ($nav == V_B_SUBM) {
            $res[H_TYPE]=H_T_SUBMIT;
            $res[H_LABEL]=$this->getLbl($viewState);
        } else {
            $res[H_TYPE]=H_T_LINK;
            $res[H_LABEL]=$this->getLbl($nav);
            if ($nav==V_B_CANC) {
                $nav=V_S_READ;
            }
            if ($nav==V_B_RFCH) {
                $nav=V_S_SLCT;
            }
            $prm=[];
            if (!is_null($this->_name)) {
                $prm['View']=$this->_name;
            }
            $path = $this->handle->getActionUrl($nav, $prm);
            if (is_null($path)) {
                return false;
            }
            $res[H_NAME]=$path;
        }
        return $res;
    }
    
    public function menuTop($spec, $viewState)
    {
        $res=[];
        $nav=$spec[V_P_VAL];
        $path=$spec[V_OBJ];
        $res[H_TYPE]=H_T_LINK;
        $sessHdl = $this->handle->getSessionHdl();
        try {
            $hdl = new Handle($path, $sessHdl);
        } catch (Exception $e) {
            return false;
        }
        $res[H_NAME]=$hdl->getUrl();
        if ($hdl->nullObj()) {
            $mod='Home';
        } else {
            $mod=$hdl->getModName();
        }
        $res[H_LABEL]=$this->getLbl($mod);
        return $res;
    }
    
    public function menuCref($spec, $viewState)
    {
        $result=[];
        $nav=$spec[V_P_VAL];
        $result[H_LABEL]=$this->getLbl($nav);
        $attr=$spec[V_ATTR];
        $prm = [];
        if (!is_null($this->_name)) {
            $prm['View']=$this->_name;
        }
        switch ($nav) {
            case V_B_NEW:
                $result[H_TYPE]=H_T_LINK;
                $path=$this->handle->getCrefUrl($attr, V_S_CREA, $prm);
                if (is_null($path)) {
                    return false;
                }
                $result[H_NAME]=$path;
                break;
            case V_C_TYP1:
                $id=$spec[V_ID];
                $nh=$this->handle->getCref($attr, $id);
                if (is_null($nh)) {
                    return false;
                }
                $v = new View($nh);
                $result[H_TYPE]=H_T_LINK;
                $result[H_NAME]=$nh->getUrl([]);
                $result[H_LABEL]=$res = $v->buildView(V_S_REF, true);
                break;
            default:
                $pos = $spec[V_ID];
                $prm[$attr] = $pos;
                $path=$this->handle->getUrl($prm, $prm);
                if ($viewState == V_S_SLCT) {
                    $result[H_TYPE]=H_T_SUBMIT;
                    $result[H_BACTION]=$path;
                }
                if ($viewState == V_S_READ) {
                    $result[H_TYPE]=H_T_LINK;
                    $result[H_NAME]=$path;
                }
        }
        return $result;
    }
    
    public function evalo($dspec, $viewState)
    {
        $name = $this->handle->getModName();
        $res =[H_TYPE =>H_T_PLAIN, H_DEFAULT=>$name];
        return $res;
    }
    
    public function subst($spec, $viewState)
    {
        $type = $spec[V_TYPE];
        $result=[];
        switch ($type) {
            case V_ELEM:
                $result= $this->element($spec, $viewState);
                break;
            case V_OBJVIEWMENU:
                $result= $this->menuObjView($spec, $viewState);
                break;
            case V_OBJACTIONMENU:
                $result= $this->menuObjAction($spec, $viewState);
                break;
            case V_TOPMENU:
                $result= $this->menuTop($spec, $viewState);
                break;
            case V_OBJ:
                $result= $this->evalo($spec, $viewState);
                break;
            case V_LIST:
                $lt = "";
                if (isset($spec[V_LT])) {
                    $lt=$spec[V_LT];
                }
                $result[H_TYPE]=$this->getHtmlList($lt, $viewState);
                $result[H_SEPARATOR]= ' ';
                if (isset($spec[V_ATTR])) {
                    $attr = $spec[V_ATTR];
                    $aspec = $this->getAttrHtml($attr, $viewState);
                    if (is_array($aspec) and isset($aspec[$lt])) {
                        if (is_array($aspec[$lt])) {
                            $result=$aspec[$lt];
                        } else {
                            $result[H_TYPE]=$aspec[$lt];
                        }
                    }
                }
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
                $result[H_ACTION]="POST";
                $hid = [];
                if (!is_null($this->_name)) {
                    $hid['View']=$this->_name;
                }
                $path = $this->handle->getUrl($hid);
                $result[H_HIDDEN]=['vnum'=>$this->handle->getVal('vnum')];
                if ($vn = $this->handle->getPrm('vnum', false)) {
                    $result[H_HIDDEN]=['vnum'=>$vn];
                }
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
            case V_CREFMENU:
                    $result= $this->menuCref($spec, $viewState);
                break;
            case V_PLAIN:
            case V_ERROR:
                $result[H_TYPE]=H_T_PLAIN;
                $result[H_DEFAULT]=$spec[V_STRING];
                break;
        }
        return $result;
    }

    public function show($viewState, $show = true)
    {
        $r = $this->buildView($viewState, false);
        if ($viewState != V_S_REF and $viewState != V_S_CREF) {
            $r=genHTML($r, $show);
        } else {
            $r=genFormElem($r, $show);
            if ($viewState == V_S_REF and ctype_space($r)) {
                $r=$this->handle->getid();
            }
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
        if ($viewState == V_S_REF and ctype_space($r)) {
            $r=$this->handle->getid();
        }
        return $r;
    }
  
    public function initView($handle, $viewState, $rec)
    {
        if ($handle->nullObj()) {
            return true;
        }
        if (!$rec) {
            $this->_name=$this->handle->getPrm('View');
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
 
    public function setView($spec, $viewState)
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
                $this->setMenuObjAction($specma[$viewState], $viewState);
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
                foreach ($specma as $viewN => $vspec) {
                    $viewL[]=$viewN;
                    if (is_null($this->_name) and $first) {
                        $this->_name=$viewN;
                    }
                    if ($this->_name==$viewN) {
                        $this->setView($vspec, $viewState);
                    }
                    $first=false;
                }
                $this->setMenuObjView($viewL, $viewState);
            }
        }
    }
    
    public function buildView($viewState, $rec)
    {
        $spec=[];
        $specL=[];
        $specS=[];
        $arg = [];
        
        if (!is_null($this->handle)) {
            $this->initView($this->handle, $viewState, $rec);
        }
        if (is_null($this->handle) or $this->handle->nullObj()) {
            $topMenu= $this->getTopMenu($viewState);
            $arg[]= [V_TYPE=>V_LIST,V_LT=>V_TOPMENU,V_ARG=>$topMenu];
            $speci = [V_TYPE=>V_LIST,V_LT=>V_OBJ,V_ARG=>$arg];
            $r=$this->subst($speci, $viewState);
            return $r;
        }
        foreach ($this->getAttrList($viewState) as $attr) {
            $view =[];
            $typ= $this->handle->getTyp($attr);
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
                $list = $this->handle->getVal($attr);
                $specL[]=$this->buildList($attr, $list, $viewState);
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
        $topMenu= $this->getTopMenu($viewState);
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_TOPMENU,V_ARG=>$topMenu];
        $menuObjView[]=[V_TYPE=>V_OBJ];
        $menuObjView = array_merge($menuObjView, $this->getMenuObjView($viewState));
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_VLIST,V_ARG=>$menuObjView];
        $menuObjAction = $this->getMenuObjAction($viewState);
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_OBJACTIONMENU,V_ARG=>$menuObjAction];
        $arg[]= [V_TYPE=>V_LIST,V_LT=>V_ALIST,V_ARG=>$spec];
        if ($viewState == V_S_SLCT) {
            $list=$this->handle->select();
            $specS[]=$this->buildList(V_S_SLCT, $list, $viewState);
            $arg[] = [V_TYPE=>V_LIST,V_LT=>V_CLIST,V_ARG=>$specS];
        }
        if ($this->handle->isErr()) {
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
    
    protected function buildList($attr, $list, $viewState)
    {
        $view = [];
        $prm = $this->getAttrHtml($attr, $viewState);
        $view[]=[V_TYPE=>V_ELEM,V_ATTR => $attr, V_PROP => V_P_LBL];
        $ctyp=$prm[V_CTYP];
        if ($ctyp == V_C_TYPN or count($list)==0) {
            if ($attr != V_S_SLCT) {
                $view[]=[V_TYPE=>V_CREFMENU,V_ATTR => $attr,V_P_VAL=>V_B_NEW];
            }
        }
        if ($ctyp == V_C_TYP1 and count($list)>0) {
            $view[]=[V_TYPE=>V_CREFMENU,V_ATTR => $attr,V_P_VAL=>V_C_TYP1,V_ID=>$list[0]];
        }
        $valList=[];
        if ($ctyp==V_C_TYPN) {
            $res = $this->getSlice($attr, $list, $view, $viewState, $prm);
            $view = $res[0];
            $valList=$res[1];
        }
        $specElm= [[V_TYPE=>V_LIST,V_LT=>V_CREF_MLIST,V_ARG=>$view]];
        if ($valList !=[]) {
            $specElm[]=$valList;
        }
        return [V_TYPE=>V_LIST,V_LT=>V_CREF,V_ARG=>$specElm];
    }
        
    protected function getSlice($attr, $list, $viewL, $viewState, $prm)
    {
        $view[]=$viewL[0];
        $c=count($list);
        $pos=0;
        $slice = $prm[H_SLICE];
        $npos = $this->handle->getPrm($attr); //not work
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
        $countf=$prm[V_COUNTF];
        if ($countf) {
            $ind = $c;
            if ($c > $slice) {
                $nc=count($list)+$pos;
                $ind=$c.' : '.$pos.'-'.$nc;
            }
            $view[]=[V_TYPE=>V_PLAIN,V_STRING=>$ind];
        }
        if ($c > $slice) {
            $npos= $pos+$slice;
            if ($npos>=$c) {
                $npos=$pos;
            }
            $view[]=
            [V_TYPE=>V_CREFMENU,V_ATTR => $attr,V_P_VAL=>V_B_PRV,V_ID=>-$pos];
            $view[]=
            [V_TYPE=>V_CREFMENU,V_ATTR => $attr,V_P_VAL=>V_B_NXT,V_ID=>$npos];
        }
        $first = true;
        foreach ($viewL as $elm) {
            if (! $first) {
                $view[] = $elm;
            }
            $first = false;
        }
        $res=[$view,[V_TYPE=>V_LIST,V_LT=>V_CVAL,V_ATTR => $attr,V_ARG=>$viewe]];
        return $res;
    }
    
    public function viewErr()
    {
        $log = $this->handle->getErrLog();
        $c=$log->logSize();
        if (!$c) {
            return 0;
        }
        $result=[];
        for ($i=0; $i<$c; $i++) {
            $r = $log->getLine($i);
            $r = CstError::subst($r);
            $result[$i]=[V_TYPE=>V_ERROR,V_STRING=>$r];
        }
        $result = [V_TYPE =>V_LIST,V_LT=>V_ERROR,V_ARG=>$result];
        return $result;
    }
}
