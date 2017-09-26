<?php
namespace ABridge\ABridge\View;

use ABridge\ABridge\View\Vew;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\View\GenHTML;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

use Exception;

class View
{
    // property

    protected $handle;
    
    protected $name;

    protected $attrHtml= [];
                
    protected $attrList;
    
    protected $htmlList = [
                CstView::V_VLIST         => CstHTML::H_T_1TABLE,
                CstView::V_OBJ           => CstHTML::H_T_LIST_BR,
                CstView::V_TOPMENU       => CstHTML::H_T_1TABLE,
                CstView::V_OBJACTIONMENU => CstHTML::H_T_1TABLE,
                CstView::V_ALIST         => CstHTML::H_T_TABLE,
                CstView::V_ATTR          => CstHTML::H_T_LIST,
                CstView::V_CLIST         => CstHTML::H_T_LIST_BR,
                CstView::V_CREF          => CstHTML::H_T_LIST_BR,
                CstView::V_CREF_MLIST    => CstHTML::H_T_1TABLE,
                CstView::V_CVAL          => CstHTML::H_T_TABLE,
                CstView::V_S_REF         => CstHTML::H_T_CONCAT, // do not change
                CstView::V_S_CREF        => CstHTML::H_T_LIST_BR, // caller or callee ?
                CstView::V_ERROR         => CstHTML::H_T_LIST,
                ];

    protected $nav=[
              CstMode::V_S_READ =>[
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstMode::V_S_UPDT],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstMode::V_S_DELT],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstMode::V_S_CREA],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstMode::V_S_SLCT],
                ],
              CstMode::V_S_SLCT =>[
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_SUBM],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_RFCH],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_CANC],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstMode::V_S_CREA],
                ],
              CstMode::V_S_CREA =>[
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_SUBM],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_CANC],
                ],
              CstMode::V_S_UPDT =>[
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_SUBM],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_CANC],
                ],
              CstMode::V_S_DELT =>[
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_SUBM],
                [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>CstView::V_B_CANC],
                ],
              ];

    protected $topMenu=[];
    protected $navView=[];
    
    protected $attrProp=[
              CstMode::V_S_READ =>[CstView::V_P_LBL,CstView::V_P_VAL],
              CstMode::V_S_SLCT =>[CstView::V_P_LBL,CstView::V_P_OP,CstView::V_P_VAL],
              CstMode::V_S_CREA =>[CstView::V_P_LBL,CstView::V_P_VAL],
              CstMode::V_S_UPDT =>[CstView::V_P_LBL,CstView::V_P_VAL],
              CstMode::V_S_DELT =>[CstView::V_P_LBL,CstView::V_P_VAL],
              CstView::V_S_REF  =>[CstView::V_P_VAL],
              CstView::V_S_CREF =>[CstView::V_P_VAL],
              ];
              
    protected $attrLbl = [];
    protected $modLbl = [];
    
    public static function init($appPrm, $specv)
    {
        foreach ($specv as $mod => $specm) {
            if ($mod != 'Home' and $mod !='MenuExcl' and $mod !='modLblList') {
                $speci = Vew::get()->getViewPrm($mod);
                if ($speci) {
                    $speciV = [];
                    if (isset($speci['viewList'])) {
                        $speciV= $speci['viewList'];
                    }
                    $specmV = [];
                    if (isset($specm['viewList'])) {
                        $specmV= $specm['viewList'];
                    }
                    $speci['viewList']=array_merge($speciV, $specmV);
                } else {
                    $speci=$specm;
                }
                Vew::get()->setViewHandler($mod, $speci);
            } else {
                Vew::get()->setViewHandler($mod, $specm);
            }
        }
    }
    
    
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
            case CstView::V_S_REF:
                $dspec = ['id'];
                return $dspec;
            case CstMode::V_S_SLCT:
                $res = array_diff(
                    $this->handle->getAttrList(),
                    ['vnum','ctstp','utstp']
                );
                foreach ($res as $attr) {
                    $atyp=$this->handle->getTyp($attr);
                    $tmp = $this->handle->isTmp($attr);
                    if ($atyp != Mtype::M_CREF and Mtype::isStruct($atyp) and !$tmp) {
                        $dspec[]=$attr;
                    }
                }
                return $dspec;
            case CstView::V_S_CREF:
                $res = array_diff(
                    $this->handle->getAttrList(),
                    ['vnum','ctstp','utstp']
                );
                $ref = $this->getAttrList(CstView::V_S_REF);
                $key = array_search('id', $ref);
                if ($key!==false) {
                    unset($ref[$key]);
                }
                $res = array_diff($res, $ref);
                foreach ($res as $attr) {
                    $atyp=$this->handle->getTyp($attr);
                    if ($atyp != Mtype::M_CREF and Mtype::isStruct($atyp)) {
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
        return CstHTML::H_T_LIST;
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
        $res = [CstView::V_P_LBL,CstView::V_P_VAL];
        return $res;
    }

    public function setMenuObjView($dspec, $viewState)
    {
        $this->navView=[];
        foreach ($dspec as $viewN) {
            $this->navView[]=
            [CstView::V_TYPE=>CstView::V_OBJVIEWMENU,CstView::V_P_VAL=>$viewN];
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
            $action =  CstMode::V_S_SLCT;
            if (is_array($classN)) {
                $path=$classN[0];
                $action = $classN[1];
            } else {
                $path=$classN;
            }
            $this->topMenu[]=
            [CstView::V_TYPE=>CstView::V_TOPMENU,CstView::V_OBJ=>$path,CstView::V_P_VAL=>$action];
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
            $navList[]= [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>$navE];
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
    
    public function setModLblList($dspec)
    {
        $this->modLbl= $dspec;
        return true;
    }
    
    public function getModLbl($mod)
    {
        foreach ($this->modLbl as $x => $lbl) {
            if ($x==$mod) {
                return $lbl;
            }
        }
        return $mod;
    }
    
    public function getProp($attr, $prop)
    {
        switch ($prop) {
            case CstView::V_P_LBL:
                return $this->getLbl($attr);
                break;
            case CstView::V_P_VAL:
                $val = $this->handle->getVal($attr);
                return $val;
                break;
            case CstView::V_P_TYPE:
                return $this->handle->getTyp($attr);
                break;
            case CstView::V_P_NAME:
                return $attr;
                break;
            case CstView::V_P_OP:
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
        
        if ($attr == CstMode::V_S_SLCT) { //bof
            $typ = Mtype::M_CREF;
        } else {
            $typ = $this->handle->getTyp($attr);
        }
        if ($typ == Mtype::M_REF) {
            return CstView::V_S_REF;
        }
        if ($typ == Mtype::M_CREF) {
            if ($attr!= CstMode::V_S_SLCT and $this->handle->isOneCref($attr)) {
                return [CstView::V_CTYP=>CstView::V_C_TYP1];
            }
            return [
                    CstView::V_SLICE=>10,CstView::V_COUNTF=>true,
                    CstView::V_CTYP=>CstView::V_C_TYPN,
                    CstView::V_CVAL=>CstHTML::H_T_TABLE
                    
            ];
        }
        if ($typ == Mtype::M_HTML) {
            return CstHTML::H_T_PLAIN;
        }
        if (! Mtype::isStruct($typ)) {
            return CstHTML::H_T_TEXTAREA;
        }
        return CstHTML::H_T_PLAIN;
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
        $res=CstHTML::H_T_TEXT;
        if (!Mtype::isStruct($typ)) {
            $res=CstHTML::H_T_TEXTAREA;
        }
        if ($typ == Mtype::M_CODE) {
            $res=CstHTML::H_T_SELECT;
        }
        return $res;
    }
    
    public function element($spec, $viewState)
    {
        $attr = $spec[CstView::V_ATTR];
        if ($attr == CstMode::V_S_SLCT) { //bof
            $typ = Mtype::M_CREF;
        } else {
            $typ = $this->handle->getTyp($attr);
        }
        $prop = $spec[CstView::V_PROP];
        $res = [];
        
        if ($prop == CstView::V_P_OP and $viewState == CstMode::V_S_SLCT) {
            if ($this->handle->isProtected($attr)) {
                $res[CstHTML::H_TYPE]=CstHTML::H_T_PLAIN;
                $res[CstHTML::H_DEFAULT]= '';
                return $res;
            }
            $res[CstHTML::H_TYPE]=CstHTML::H_T_SELECT;
            $name=$attr.'_OP';
            $res[CstHTML::H_NAME]=$name;
            $res[CstHTML::H_VALUES]=[['=','='],['>','>'],['<','<']];
            if ($typ == Mtype::M_CODE) {
                $res[CstHTML::H_VALUES]=[['=','=']];
            }
            if ($typ == Mtype::M_STRING) {
                $res[CstHTML::H_VALUES]=[['::','::'],['=','='],['>','>'],['<','<']];
            }
            $default=$this->handle->getPrm($name);
            if (is_null($default)) {
                $default= '=';
            }
            $res[CstHTML::H_DEFAULT]=$default;
            return $res;
        }
        
        if ($prop==CstView::V_P_VAL and
            ((($viewState == CstMode::V_S_CREA or $viewState == CstMode::V_S_UPDT)
             and $this->handle->isModif($attr))
            or
            ($viewState == CstMode::V_S_SLCT
             and $this->handle->isSelect($attr)))) {
            $res[CstHTML::H_NAME]=$attr;
            $default=$this->handle->getPrm($attr, Mtype::isRaw($typ));
            if (is_null($default)) {
                if ($viewState == CstMode::V_S_UPDT) {
                    $default=$this->handle->getVal($attr);
                }
                if ($viewState == CstMode::V_S_CREA) {
                    $default=$this->handle->getDflt($attr);
                }
            }
            $res[CstHTML::H_DEFAULT]=$default;
            $htyp = $this->getUpAttrHtml($attr, $viewState);
            if (is_array($htyp)) {
                foreach ($htyp as $elmT => $elmV) {
                    $res[$elmT]=$elmV;
                }
            } else {
                $res[CstHTML::H_TYPE]=$htyp;
            }
            if ($htyp == CstHTML::H_T_SELECT or $htyp == CstHTML::H_T_RADIO) {
                $vals=$this->handle->getValues($attr);
                $values=[];
                if ($htyp == CstHTML::H_T_SELECT
                and ((!$this->handle->isMdtr($attr))
                or $viewState == CstMode::V_S_SLCT)) {
                    $values[] = ["",""];
                }
                foreach ($vals as $v) {
                    $m = $this->handle->getCode($attr, (int) $v);
                    if (! is_null($m)) {
                        $vw = new View($m);
                        $l = $vw->showRec(CstView::V_S_REF);
                        $r = [$v,$l];
                        $values[]=$r;
                    }
                }
                $res[CstHTML::H_VALUES]=$values;
            }
            return $res ;
        }
        if ($viewState == CstView::V_S_CREF and $typ == Mtype::M_REF
        and $this->handle->isMainRef($attr)
        ) {
            return false;
        }
        
        if ($typ != Mtype::M_CREF or $prop != CstView::V_P_VAL) {
            $x=$this->getProp($attr, $prop);
        }
        if ($prop==CstView::V_P_VAL) {
            if ($attr == 'id' and $viewState == CstView::V_S_CREF) {
                $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
                $res[CstHTML::H_LABEL]=$this->showRec(CstView::V_S_REF);
                $res[CstHTML::H_NAME]=$this->handle->getUrl();
                return $res;
            }
            if ($typ==Mtype::M_CREF) {
                $id=$spec[CstView::V_ID];
                if ($attr != CstMode::V_S_SLCT) {
                    $nh=$this->handle->getCref($attr, $id);
                } else {
                    $nh=$this->handle->getObjId($id);
                }
                if (is_null($nh)) {
                    return false;
                }
                $v = new View($nh);
                $res = $v->buildView(CstView::V_S_CREF, true);
                return $res;
            }
            if ($typ==Mtype::M_REF) {
                $nh=$this->handle->getRef($attr);
                if (is_null($nh)) {
                    return [CstHTML::H_TYPE =>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>""];
                }
                if ($this->handle->isProtected($attr)) {
                    $rep=CstView::V_S_REF;
                } else {
                    $rep=$this->getAttrHtml($attr, $viewState);
                }
                if (is_array($rep)) {
                    $res=$rep;
                } else {
                    $res[CstHTML::H_TYPE]= $rep;
                }
                if ($rep==CstView::V_S_REF) {
                    $refpath = $nh->getUrl();
                    $res[CstHTML::H_TYPE]= CstHTML::H_T_LINK;
                    $res[CstHTML::H_NAME]=$refpath;
                    if (is_null($refpath)) {
                        $res[CstHTML::H_TYPE]= CstHTML::H_T_PLAIN;
                    }
                }
                if ($rep == CstHTML::H_T_PLAIN) {
                    $rep=CstView::V_S_REF;
                }
                if ($rep != CstView::V_S_REF and $rep != CstHTML::H_T_PLAIN) {
                    $res[CstHTML::H_TYPE] = CstHTML::H_T_PLAIN;
                }
                $v = new View($nh);
                $res[CstHTML::H_LABEL]=$v->showRec($rep);
                $res[CstHTML::H_DEFAULT]=$res[CstHTML::H_LABEL];
                return $res;
            }
            if ($typ==Mtype::M_CODE and (!is_null($x))) {
                $nh=$this->handle->getCode($attr, (int) $x);
                $v = new View($nh);
                $x = $v->showRec(CstView::V_S_REF);
            }
        }
        if ($prop==CstView::V_P_VAL) {
            $htyp = $this->getAttrHtml($attr, $viewState);
            if (is_array($htyp)) {
                $res= $htyp;
            } else {
                $res[CstHTML::H_TYPE] = $htyp;
            }
            if ($res[CstHTML::H_TYPE]==CstHTML::H_T_LINK) {
                $res[CstHTML::H_NAME]=$x;
                $res[CstHTML::H_LABEL]=$x;
            } else {
                $res[CstHTML::H_DEFAULT]=$x;
                $res[CstHTML::H_DISABLED]=true;
            }
            if ($res[CstHTML::H_TYPE]==CstHTML::H_T_IMG) {
                $tres=$res;
                $res=[];
                $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
                $pict = 'C:\xampp\htdocs'.$x;
                if ($x) {
                    list($width, $height, $itype, $iattr) = getimagesize($pict);
                } else {
                    $width = 1;
                    $height = 1;
                }
                $res[CstHTML::H_NAME]=$x;
                $rf = 1;
                if (isset($tres[CstHTML::H_ROWP])) {
                    $rf= $tres[CstHTML::H_ROWP]/$height;
                }
                $cf = 1;
                if (isset($tres[CstHTML::H_COLP])) {
                    $cf = $tres[CstHTML::H_COLP]/$width;
                }
                if ($rf > $cf) {
                    $cf=$rf;
                } else {
                    $rf=$cf;
                }
                $tres[CstHTML::H_ROWP]= round($height * $rf);
                $tres[CstHTML::H_COLP]= round($width * $cf);
                $res[CstHTML::H_LABEL]=$tres;
            }
            return $res;
        }
        $res =[CstHTML::H_TYPE =>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>$x];
        return $res;
    }
    
    public function menuObjView($spec, $viewState)
    {
        $viewn=$spec[CstView::V_P_VAL];
        $res= [];
        if ($viewState != CstMode::V_S_READ) {
            return false;
        }
        if ($viewn != $this->_name) {
            $res[CstHTML::H_LABEL]=$this->getLbl($viewn);
            $res[CstHTML::H_NAME]=$this->handle->getUrl(['View'=>$viewn]);
            $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
        } else {
            $res[CstHTML::H_TYPE]=CstHTML::H_T_PLAIN;
            $res[CstHTML::H_DEFAULT]=$this->getLbl($viewn);
        }
        return $res;
    }

    public function menuObjAction($nav, $viewState)
    {
        $res=[];
        $nav=$nav[CstView::V_P_VAL];
        if ($nav == CstView::V_B_SUBM) {
            $res[CstHTML::H_TYPE]=CstHTML::H_T_SUBMIT;
            $res[CstHTML::H_LABEL]=$this->getLbl($viewState);
        } else {
            $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
            $res[CstHTML::H_LABEL]=$this->getLbl($nav);
            if ($nav==CstView::V_B_CANC) {
                $nav=CstMode::V_S_READ;
            }
            if ($nav==CstView::V_B_RFCH) {
                $nav=CstMode::V_S_SLCT;
            }
            $prm=[];
            if (!is_null($this->_name)) {
                $prm['View']=$this->_name;
            }
            $path = $this->handle->getActionUrl($nav, $prm);
            if (is_null($path)) {
                return false;
            }
            $res[CstHTML::H_NAME]=$path;
        }
        return $res;
    }
    
    public function menuTop($spec, $viewState)
    {
        $res=[];
        $nav=$spec[CstView::V_P_VAL];
        $path=$spec[CstView::V_OBJ];
        $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
        try {
            $hdl = $this->handle->getNewHdl($path);
        } catch (Exception $e) {
            return false;
        }
        $res[CstHTML::H_NAME]=$hdl->getUrl();
        if ($hdl->nullObj()) {
            $mod='Home';
        } else {
            $mod=$hdl->getModName();
        }
        $res[CstHTML::H_LABEL]=$this->getModLbl($mod);
        return $res;
    }
    
    public function menuCref($spec, $viewState)
    {
        $result=[];
        $nav=$spec[CstView::V_P_VAL];
        $result[CstHTML::H_LABEL]=$this->getLbl($nav);
        $attr=$spec[CstView::V_ATTR];
        $prm = [];
        if (!is_null($this->_name)) {
            $prm['View']=$this->_name;
        }
        switch ($nav) {
            case CstView::V_B_NEW:
                $result[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
                $path=$this->handle->getCrefUrl($attr, CstMode::V_S_CREA, $prm);
                if (is_null($path)) {
                    return false;
                }
                $result[CstHTML::H_NAME]=$path;
                break;
            case CstView::V_C_TYP1:
                $id=$spec[CstView::V_ID];
                $nh=$this->handle->getCref($attr, $id);
                if (is_null($nh)) {
                    return false;
                }
                $v = new View($nh);
                $result[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
                $result[CstHTML::H_NAME]=$nh->getUrl([]);
                $result[CstHTML::H_LABEL]=$res = $v->buildView(CstView::V_S_REF, true);
                break;
            default:
                $pos = $spec[CstView::V_ID];
                $prm[$attr] = $pos;
                $path=$this->handle->getUrl($prm, $prm);
                if ($viewState == CstMode::V_S_SLCT) {
                    $result[CstHTML::H_TYPE]=CstHTML::H_T_SUBMIT;
                    $result[CstHTML::H_BACTION]=$path;
                }
                if ($viewState == CstMode::V_S_READ) {
                    $result[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
                    $result[CstHTML::H_NAME]=$path;
                }
        }
        return $result;
    }
    
    public function evalo($dspec, $viewState)
    {
        $name = $this->handle->getModName();
        $res =[CstHTML::H_TYPE =>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>$name];
        return $res;
    }
    
    public function subst($spec, $viewState)
    {
        $type = $spec[CstView::V_TYPE];
        $result=[];
        switch ($type) {
            case CstView::V_ELEM:
                $result= $this->element($spec, $viewState);
                break;
            case CstView::V_OBJVIEWMENU:
                $result= $this->menuObjView($spec, $viewState);
                break;
            case CstView::V_OBJACTIONMENU:
                $result= $this->menuObjAction($spec, $viewState);
                break;
            case CstView::V_TOPMENU:
                $result= $this->menuTop($spec, $viewState);
                break;
            case CstView::V_OBJ:
                $result= $this->evalo($spec, $viewState);
                break;
            case CstView::V_LIST:
                $lt = "";
                if (isset($spec[CstView::V_LT])) {
                    $lt=$spec[CstView::V_LT];
                }
                $result[CstHTML::H_TYPE]=$this->getHtmlList($lt, $viewState);
                $result[CstHTML::H_SEPARATOR]= ' ';
                if (isset($spec[CstView::V_ATTR])) {
                    $attr = $spec[CstView::V_ATTR];
                    $aspec = $this->getAttrHtml($attr, $viewState);
                    if (is_array($aspec) and isset($aspec[$lt])) {
                        if (is_array($aspec[$lt])) {
                            $result=$aspec[$lt];
                        } else {
                            $result[CstHTML::H_TYPE]=$aspec[$lt];
                        }
                    }
                }
                $arg=[];
                foreach ($spec[CstView::V_ARG] as $elem) {
                    $r=$this->subst($elem, $viewState);
                    if ($r) {
                        $arg[]=$r;
                    }
                }
                $result[CstHTML::H_ARG]=$arg;
                break;
            case CstView::V_FORM:
                $result[CstHTML::H_TYPE]=CstHTML::H_T_FORM;
                $result[CstHTML::H_ACTION]="POST";
                $hid = [];
                if (!is_null($this->_name)) {
                    $hid['View']=$this->_name;
                }
                $path = $this->handle->getUrl($hid);
                $result[CstHTML::H_HIDDEN]=['vnum'=>$this->handle->getVal('vnum')];
                if ($vn = $this->handle->getPrm('vnum', false)) {
                    $result[CstHTML::H_HIDDEN]=['vnum'=>$vn];
                }
                $result[CstHTML::H_URL]=$path;
                $arg=[];
                foreach ($spec[CstView::V_ARG] as $elem) {
                    $r=$this->subst($elem, $viewState);
                    if ($r) {
                        $arg[]=$r;
                    }
                }
                $result[CstHTML::H_ARG]=$arg;
                break;
            case CstView::V_CREFMENU:
                    $result= $this->menuCref($spec, $viewState);
                break;
            case CstView::V_PLAIN:
            case CstView::V_ERROR:
                $result[CstHTML::H_TYPE]=CstHTML::H_T_PLAIN;
                $result[CstHTML::H_DEFAULT]=$spec[CstView::V_STRING];
                break;
        }
        return $result;
    }

    public function show($viewState, $show = true)
    {
        $this->initMenu();
        $r = $this->buildView($viewState, false);
        $r=GenHTML::genHTML($r, $show);
        return $r;
    }
  
    protected function showRec($name)
    {
        if ($name != CstView::V_S_REF and $name != CstView::V_S_CREF) {
            $this->_name=$name;
            $viewState=CstMode::V_S_READ;
        } else {
            $viewState=$name;
        }
        $r = $this->buildView($viewState, true);
        $r=GenHTML::genFormElem($r, false);
        if ($viewState == CstView::V_S_REF and ctype_space($r)) {
            $r=$this->handle->getid();
        }
        return $r;
    }

    public function initMenu()
    {
        $menu = $this->getTopMenu(null);
        if ($menu != []) {
            return;
        }
        $home = [];
        $res = Vew::get()->getViewPrm('Home');
        if ($res) {
            $home=$res;
        }
        $selmenu = $this->handle->getSelPath();
        $rmenu=[];
        $res = Vew::get()->getViewPrm('MenuExcl');
        if ($res) {
            $rmenu=$res;
        }
        $selmenu= array_diff($selmenu, $rmenu);
        $menu = array_unique(array_merge($home, $selmenu));
        $this->setTopMenu($menu);
        
        $res = Vew::get()->getViewPrm('modLblList');
        if ($res) {
            $this->setModLblList($res);
        }
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
        $spec = Vew::get()->getViewPrm($modName);
        if (is_null($spec)) {
                return true;
        }
        $this->setView($spec, $viewState);
        if ($viewState == CstView::V_S_CREF) {
            $this->initView($handle, CstView::V_S_REF, true);
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

        if ($viewState !=  CstView::V_S_REF and $viewState !=  CstView::V_S_CREF) {
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
            $arg[]= [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_TOPMENU,CstView::V_ARG=>$topMenu];
            $speci = [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_OBJ,CstView::V_ARG=>$arg];
            $r=$this->subst($speci, $viewState);
            return $r;
        }
        foreach ($this->getAttrList($viewState) as $attr) {
            $view =[];
            $typ= $this->handle->getTyp($attr);
            if ($typ != Mtype::M_CREF) {
                foreach ($this->getPropList($viewState) as $prop) {
                    $view[] = [CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr, CstView::V_PROP => $prop];
                }
                if ($viewState == CstView::V_S_REF or $viewState == CstView::V_S_CREF) {
                    $spec = array_merge($spec, $view);
                } else {
                    $spec[]=[CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_ATTR,CstView::V_ARG=>$view];
                }
            }
            if ($typ == Mtype::M_CREF) {
                $list = $this->handle->getVal($attr);
                $specL[]=$this->buildList($attr, $list, $viewState);
            }
        }
        if ($viewState == CstView::V_S_REF or $viewState == CstView::V_S_CREF) {
            $specf = [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>$viewState,CstView::V_ARG=>$spec];
            $r=$this->subst($specf, $viewState);
            return $r;
        }
        if ($rec) {
            $specf = [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_ALIST,CstView::V_ARG=>$spec];
            $r=$this->subst($specf, $viewState);
            return $r;
        }
        $arg = [];
        $topMenu= $this->getTopMenu($viewState);
        $arg[]= [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_TOPMENU,
                CstView::V_ARG=>$topMenu
                
        ];
        $menuObjView[]=[CstView::V_TYPE=>CstView::V_OBJ];
        $menuObjView = array_merge($menuObjView, $this->getMenuObjView($viewState));
        $arg[]= [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_VLIST,
                CstView::V_ARG=>$menuObjView];
        
        $menuObjAction = $this->getMenuObjAction($viewState);
        $arg[]= [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_OBJACTIONMENU,
                CstView::V_ARG=>$menuObjAction
                
        ];
        $arg[]= [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_ALIST,
                CstView::V_ARG=>$spec
                
        ];
        if ($viewState == CstMode::V_S_SLCT) {
            $list=$this->handle->select();
            $specS[]=$this->buildList(CstMode::V_S_SLCT, $list, $viewState);
            $arg[] = [
                    CstView::V_TYPE=>CstView::V_LIST,
                    CstView::V_LT=>CstView::V_CLIST,
                    CstView::V_ARG=>$specS
                    
            ];
        }
        if ($this->handle->isErr()) {
            $e = $this->viewErr();
            $arg[]=$e;
        }
        if ($viewState == CstMode::V_S_CREA
        or  $viewState == CstMode::V_S_DELT
        or  $viewState == CstMode::V_S_UPDT
        or  $viewState == CstMode::V_S_SLCT) {
            $speci = [
                    CstView::V_TYPE=>CstView::V_FORM,
                    CstView::V_LT=>CstView::V_OBJ,
                    CstView::V_ARG=>$arg
                    
            ];
            $r=$this->subst($speci, $viewState);
            return $r;
        }
        $arg[] = [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_CLIST,
                CstView::V_ARG=>$specL
                
        ];
        $speci = [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_OBJ,
                CstView::V_ARG=>$arg
                
        ];
        $r=$this->subst($speci, $viewState);
        return $r;
    }
    
    protected function buildList($attr, $list, $viewState)
    {
        $view = [];
        $prm = $this->getAttrHtml($attr, $viewState);
        $view[]=[CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr, CstView::V_PROP => CstView::V_P_LBL];
        $ctyp=$prm[CstView::V_CTYP];
        if ($ctyp == CstView::V_C_TYPN or count($list)==0) {
            if ($attr != CstMode::V_S_SLCT) {
                $view[]=[
                        CstView::V_TYPE=>CstView::V_CREFMENU,
                        CstView::V_ATTR => $attr,
                        CstView::V_P_VAL=>CstView::V_B_NEW
                        
                ];
            }
        }
        if ($ctyp == CstView::V_C_TYP1 and count($list)>0) {
            $view[]=[
                    CstView::V_TYPE=>CstView::V_CREFMENU,
                    CstView::V_ATTR => $attr,
                    CstView::V_P_VAL=>CstView::V_C_TYP1,
                    CstView::V_ID=>$list[0]
                    
            ];
        }
        $valList=[];
        if ($ctyp==CstView::V_C_TYPN) {
            $res = $this->getSlice($attr, $list, $view, $viewState, $prm);
            $view = $res[0];
            $valList=$res[1];
        }
        $specElm= [[
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_CREF_MLIST,
                CstView::V_ARG=>$view
                
        ]];
        if ($valList !=[]) {
            $specElm[]=$valList;
        }
        return [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_CREF,
                CstView::V_ARG=>$specElm
                
        ];
    }
        
    protected function getSlice($attr, $list, $viewL, $viewState, $prm)
    {
        $view[]=$viewL[0];
        $c=count($list);
        $pos=0;
        $slice = $prm[CstView::V_SLICE];
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
            $viewe[]=[CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr,
                      CstView::V_PROP => CstView::V_P_VAL,CstView::V_ID=>$id];
        }
        $countf=$prm[CstView::V_COUNTF];
        if ($countf) {
            $ind = $c;
            if ($c > $slice) {
                $nc=count($list)+$pos;
                $ind=$c.' : '.$pos.'-'.$nc;
            }
            $view[]=[CstView::V_TYPE=>CstView::V_PLAIN,CstView::V_STRING=>$ind];
        }
        if ($c > $slice) {
            $npos= $pos+$slice;
            if ($npos>=$c) {
                $npos=$pos;
            }
            $view[]=
            [
                    CstView::V_TYPE=>CstView::V_CREFMENU,
                    CstView::V_ATTR => $attr,
                    CstView::V_P_VAL=>CstView::V_B_PRV,
                    CstView::V_ID=>-$pos
                    
            ];
            $view[]=
            [
                    CstView::V_TYPE=>CstView::V_CREFMENU,
                    CstView::V_ATTR => $attr,
                    CstView::V_P_VAL=>CstView::V_B_NXT,
                    CstView::V_ID=>$npos
                    
            ];
        }
        $first = true;
        foreach ($viewL as $elm) {
            if (! $first) {
                $view[] = $elm;
            }
            $first = false;
        }
        $res=[$view,[
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_CVAL,
                CstView::V_ATTR => $attr,
                CstView::V_ARG=>$viewe
                
        ]];
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
            $result[$i]=[
                    CstView::V_TYPE=>CstView::V_ERROR,
                    CstView::V_STRING=>$r
                    
            ];
        }
        $result = [
                CstView::V_TYPE =>CstView::V_LIST,
                CstView::V_LT=>CstView::V_ERROR,
                CstView::V_ARG=>$result
                
        ];
        return $result;
    }
}
