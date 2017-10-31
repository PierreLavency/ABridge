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
use phpDocumentor\Reflection\Types\This;

class View
{
    // property

    protected $handle;
    protected $vew;
    protected $modName;
    protected $name;

    protected $topMenu=[];
    protected $urlPrm=[];
    
    // constructors

    public function __construct($handle)
    {
        $this->handle=$handle;
        if (! $handle->nullObj()) {
            $this->modName=$handle->getModName();
        }
        $this->vew=Vew::get();
    }

    protected function getAttrList($viewState)
    {
        if ($res=$this->vew->getSpec($this->modName, $this->name, $viewState, 'attrList')) {
            return $res;
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
    
 
    protected function getHtmlList($listType, $viewState)
    {
        $htmlList = [
                CstView::V_VLIST         => [CstHTML::H_TYPE => CstHTML::H_T_1TABLE],
                CstView::V_OBJ           => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_TOPMENU       => [CstHTML::H_TYPE =>CstHTML::H_T_1TABLE],
                CstView::V_OBJACTIONMENU => [CstHTML::H_TYPE =>CstHTML::H_T_1TABLE],
                CstView::V_ALIST         => [CstHTML::H_TYPE =>CstHTML::H_T_TABLE],
                CstView::V_ATTR          => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_CLIST         => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_CREFLBL       => [CstHTML::H_TYPE =>CstHTML::H_T_TABELBL],
                CstView::V_CREF          => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_CREF_MLIST    => [CstHTML::H_TYPE =>CstHTML::H_T_1TABLE],
                CstView::V_CVAL          => [CstHTML::H_TYPE =>CstHTML::H_T_TABLE],
                CstView::V_S_REF         => [CstHTML::H_TYPE =>CstHTML::H_T_CONCAT], // do not change
                CstView::V_S_CREF        => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR], // caller or callee ?
                CstView::V_ERROR         => [CstHTML::H_TYPE =>CstHTML::H_T_LIST],
                
        ];
        
        if ($res=$this->vew->getSpec($this->modName, $this->name, $viewState, 'listHtml')) {
            if (isset($res[$listType])) {
                return $res[$listType];
            }
        }
        return $htmlList[$listType];
    }

    protected function getPropList($viewState)
    {
        $attrProp=[
                CstMode::V_S_READ =>[CstView::V_P_LBL,CstView::V_P_VAL],
                CstMode::V_S_SLCT =>[CstView::V_P_LBL,CstView::V_P_OP,CstView::V_P_VAL],
                CstMode::V_S_CREA =>[CstView::V_P_LBL,CstView::V_P_VAL],
                CstMode::V_S_UPDT =>[CstView::V_P_LBL,CstView::V_P_VAL],
                CstMode::V_S_DELT =>[CstView::V_P_LBL,CstView::V_P_VAL],
                CstView::V_S_REF  =>[CstView::V_P_VAL],
                CstView::V_S_CREF =>[CstView::V_P_VAL],
        ];
        
        if ($res=$this->vew->getSpec($this->modName, $this->name, $viewState, 'attrProp')) {
            return $res;
        }
        return $attrProp[$viewState];
    }

    protected function getMenuObjView($viewState)
    {
        if ($res = $this->vew->getViewList($this->modName, $viewState)) {
            $navView=[];
            foreach ($res as $viewN) {
                $navView[]=[CstView::V_TYPE=>CstView::V_OBJVIEWMENU,CstView::V_P_VAL=>$viewN];
            }
            return $navView;
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

    protected function getTopMenu($viewState)
    {
        if (isset($this->topMenu)) {
            return $this->topMenu;
        }
        return [];
    }

    protected function getMenuObjAction($viewState)
    {
        $nav=[
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
            ]
        ];
        
        if ($res=$this->vew->getSpec($this->modName, $this->name, $viewState, 'navList')) {
            $navList=[];
            foreach ($res as $navE) {
                $navList[]= [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>$navE];
            }
            return $navList;
        }
        return $nav[$viewState];
    }

    protected function getLbl($attr)
    {
        if ($res=$this->vew->getSpec($this->modName, $this->name, null, 'lblList')) {
            if (isset($res[$attr])) {
                return $res[$attr];
            }
        }
        return $attr;
    }

    protected function getModLbl($mod)
    {
        $lblList = $this->vew->getViewPrm('modLblList');
        if ($lblList and isset($lblList[$mod])) {
            return $lblList[$mod];
        }
        return $mod;
    }
    
    protected function getProp($attr, $prop)
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

    protected function getAttrHtml($attr, $viewState)
    {
        $defList = [
                CstView::V_SLICE=>10,
                CstView::V_CTYP=>CstView::V_C_TYPN,
                CstView::V_CVAL=>[CstHTML::H_TYPE=>CstHTML::H_T_TABLE],
                CstView::V_CREFLBL=>false,
                CstView::V_COUNTF=>true,
                CstView::V_B_NEW=>false,
                CstView::V_B_SLC=>false,
                CstView::V_B_BGN=>true,
                cstView::V_B_PRV=>true,
                CstView::V_B_NXT=>true,
                CstView::V_B_END=>true,
        ];
        $oneList = [
                CstView::V_CTYP=>CstView::V_C_TYP1,
                CstView::V_B_SLC=>true,
                CstView::V_B_NEW=>true,
        ];
        $resList=[];
        if ($res=$this->vew->getSpec($this->modName, $this->name, $viewState, 'attrHtml')) {
            if (isset($res[$attr])) {
                $resList= $res[$attr];
            }
        }
        if ($attr == CstMode::V_S_SLCT) { //bof
            return array_merge($defList, $resList);
        }
        $typ = $this->handle->getTyp($attr);
        if ($typ == Mtype::M_CREF) {
            if ($this->handle->isOneCref($attr)) {
                return array_merge($oneList, $resList);
            }
            $defList[CstView::V_B_NEW]=true;
            return array_merge($defList, $resList);
        }
        if ($resList != []) {
            return $resList;
        }
        if ($typ == Mtype::M_REF) {
            return CstView::V_S_REF;
        }
        if ($typ == Mtype::M_HTML) {
            return CstHTML::H_T_PLAIN;
        }
        if (! Mtype::isStruct($typ)) {
            return CstHTML::H_T_TEXTAREA;
        }
        return CstHTML::H_T_PLAIN;
    }
    
    protected function getUpAttrHtml($attr, $viewState)
    {
        if ($res=$this->vew->getSpec($this->modName, $this->name, $viewState, 'attrHtml')) {
            if (isset($res[$attr])) {
                return $res[$attr];
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
    
    protected function elementProp($typ, $attr, $prop, $viewState)
    {
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
        $lbl=$this->getProp($attr, $prop);
        $res =[CstHTML::H_TYPE =>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>$lbl];
        return $res;
    }
        
    protected function element($spec, $viewState)
    {
        $attr = $spec[CstView::V_ATTR];
        if ($attr == CstMode::V_S_SLCT) { //bof
            $typ = Mtype::M_CREF;
        } else {
            $typ = $this->handle->getTyp($attr);
        }
        $prop = $spec[CstView::V_PROP];
        $res = [];
        
        if ($prop != CstView::V_P_VAL) {
            return $this->elementProp($typ, $attr, $prop, $viewState);
        }
        if (((($viewState == CstMode::V_S_CREA or $viewState == CstMode::V_S_UPDT)
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
                        $l = $this->getRefLbl($m);
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
        if ($attr == 'id' and $viewState == CstView::V_S_CREF) {
            return $this->elemRefView($this->handle, CstView::V_S_REF);
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
            if (isset($spec[CstView::V_CREFLBL]) and  $spec[CstView::V_CREFLBL]) {
                return $v->getCrefLbls($this->handle, $viewState, $this->urlPrm);
            }
            return $v->buildView(CstView::V_S_CREF, true);
        }
        
        if ($typ==Mtype::M_REF) {
            return $this->elemRef($attr, $viewState);
        }
        if ($typ==Mtype::M_CODE) {
            return $this->elemCode($attr, $viewState);
        }

        $attrVal=$this->handle->getVal($attr);
        $htyp = $this->getAttrHtml($attr, $viewState);
        if (is_array($htyp)) {
            $res= $htyp;
        } else {
            $res[CstHTML::H_TYPE] = $htyp;
        }
        
        if ($res[CstHTML::H_TYPE]==CstHTML::H_T_LINK) {
            $res[CstHTML::H_NAME]=$attrVal;
            $res[CstHTML::H_LABEL]=$attrVal;
            return $res;
        }
        
        $res[CstHTML::H_DEFAULT]=$attrVal;
        $res[CstHTML::H_DISABLED]=true;
        
        if ($res[CstHTML::H_TYPE]==CstHTML::H_T_IMG) {
            $tres=$res;
            $res=[];
            $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
            $pict = 'C:\xampp\htdocs'.$attrVal;
            $width = 1;
            $height = 1;
            if ($attrVal) {
                list($width, $height, $itype, $iattr) = getimagesize($pict);
            }
            $res[CstHTML::H_NAME]=$attrVal;
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

    protected function getCrefLbls($handle, $viewState, $prm)
    {
        $lblList= [];
        $attrList=$this->getAttrList(CstView::V_S_CREF);
        if ($viewState != CstMode::V_S_SLCT) {
            foreach ($attrList as $attr) {
                $lbl=$this->getLbl($attr);
                $lblElm=[CstHTML::H_TYPE=>CstHTML::H_T_PLAIN,CstHTML::H_DEFAULT=>$lbl];
                $lblList[]=$lblElm;
            }
            return [CstHTML::H_TYPE=>CstHTML::H_T_TABELBL, CstHTML::H_ARG=>$lblList];
        }
        $attrSort = $handle->getPrm(CstView::V_P_SRT, false);
        $dsc =  $handle->getPrm(CstView::V_P_DSC, false);
        $prm = [];

        foreach ($attrList as $attr) {
            $lbl=$this->getLbl($attr);
            $prm[CstView::V_P_SRT]=$attr;
            if ($attr == $attrSort) {
                if ($dsc) {
                    $lbl = $lbl . "(-)";
                } else {
                    $lbl = $lbl . "(+)";
                    $prm[CstView::V_P_DSC]=true;
                }
            }
            $lblElm=[CstHTML::H_TYPE=>CstHTML::H_T_SUBMIT,CstHTML::H_LABEL=>$lbl];
            $lblElm[CstHTML::H_BACTION]=$handle->getUrl($prm);
            $lblList[]=$lblElm;
        }
        return [CstHTML::H_TYPE=>CstHTML::H_T_TABELBL, CstHTML::H_ARG=>$lblList];
    }
    
    protected function getRefLbl($handle)
    {
        $elmList=[];
        if ($res=$this->vew->getSpec($handle->getmodName(), null, CstView::V_S_REF, 'attrList')) {
            foreach ($res as $attr) {
                $elmList[]=$handle->getVal($attr);
            }
            $refLbl=implode(' ', $elmList);
            if (!ctype_space($refLbl)) {
                return $refLbl;
            }
        }
        return $this->handle->getid();
    }
     
    protected function elemCode($attr, $viewState)
    {
        $codeHdl=$this->handle->getCodeRef($attr);
        if (is_null($codeHdl)) {
            return [CstHTML::H_TYPE =>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>""];
        }
        $rep=$this->getAttrHtml($attr, $viewState);
        return $this->elemRefView($codeHdl, $rep);
    }
    
    protected function elemRef($attr, $viewState)
    {
        $refHdl=$this->handle->getRef($attr);
        if (is_null($refHdl)) {
            return [CstHTML::H_TYPE =>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>""];
        }
        $rep=$this->getAttrHtml($attr, $viewState);
        return $this->elemRefView($refHdl, $rep);
    }
 
    protected function elemRefView($refHdl, $rep)
    {
        // can be ref / plain / or an object view
        if ($rep==CstView::V_S_REF) {
            $refpath = $refHdl->getUrl();
            $res[CstHTML::H_TYPE]= CstHTML::H_T_LINK;
            $res[CstHTML::H_NAME]= $refpath;
            $res[CstHTML::H_LABEL]= $this->getRefLbl($refHdl);
            return $res;
        }
        if ($rep==CstHTML::H_T_PLAIN) {
            $res[CstHTML::H_TYPE]=CstHTML::H_T_PLAIN;
            $res[CstHTML::H_DEFAULT]= $this->getRefLbl($refHdl);
            return $res;
        }
        $view= new View($refHdl);
        $res[CstHTML::H_TYPE] = CstHTML::H_T_PLAIN;
        $res[CstHTML::H_DEFAULT]=$view->showRec($rep);
        return $res;
    }
    
    protected function menuObjView($spec, $viewState)
    {
        if ($viewState != CstMode::V_S_READ) {
            return false;
        }
        $viewn=$spec[CstView::V_P_VAL];
        $res= [];
        if ($viewn != $this->name) {
            $res[CstHTML::H_LABEL]=$this->getLbl($viewn);
            $res[CstHTML::H_NAME]=$this->handle->getUrl(['View'=>$viewn]);
            $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
            return $res;
        }
        $res[CstHTML::H_TYPE]=CstHTML::H_T_PLAIN;
        $res[CstHTML::H_DEFAULT]=$this->getLbl($viewn);
        return $res;
    }

    protected function menuObjAction($nav, $viewState)
    {
        $res=[];
        $nav=$nav[CstView::V_P_VAL];
        if ($nav == CstView::V_B_SUBM) {
            $res[CstHTML::H_TYPE]=CstHTML::H_T_SUBMIT;
            $res[CstHTML::H_LABEL]=$this->getLbl($viewState);
            return $res;
        }
        $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
        $res[CstHTML::H_LABEL]=$this->getLbl($nav);
        if ($nav==CstView::V_B_CANC) {
                $nav=CstMode::V_S_READ;
        }
        if ($nav==CstView::V_B_RFCH) {
                $nav=CstMode::V_S_SLCT;
        }
        $prm=[];
        if (!is_null($this->name)) {
                $prm['View']=$this->name;
        }
        $path = $this->handle->getActionUrl($nav, $prm);
        if (is_null($path)) {
                return false;
        }
        $res[CstHTML::H_NAME]=$path;
        return $res;
    }
    
    protected function menuTop($spec, $viewState)
    {
        $res=[];
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
    
    protected function menuCref($spec, $viewState)
    {
        $result=[];
        $nav=$spec[CstView::V_P_VAL];
        $result[CstHTML::H_LABEL]=$this->getLbl($nav);
        $attr=$spec[CstView::V_ATTR];
        $prm = $this->urlPrm;
        if (!is_null($this->name)) {
            $prm[CstView::V_P_VEW]=$this->name;
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
            case CstView::V_B_SLC:
                $result[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
                $path=$this->handle->getCrefUrl($attr, CstMode::V_S_SLCT, $prm);
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
                $path=$this->handle->getUrl($prm);
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
    
    protected function evalo($dspec, $viewState)
    {
        $name = $this->handle->getModName();
        $res =[CstHTML::H_TYPE =>CstHTML::H_T_PLAIN, CstHTML::H_DEFAULT=>$name];
        return $res;
    }
    
    protected function subst($spec, $viewState)
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
                $listTyp=$spec[CstView::V_LT];
                if (isset($spec[CstView::V_ATTR])) {
                    $attr = $spec[CstView::V_ATTR];
                    $aspec = $this->getAttrHtml($attr, $viewState);
                    $result=$aspec[$listTyp];
                } else {
                    $result= $this->getHtmlList($listTyp, $viewState);
                }
                $result[CstHTML::H_SEPARATOR]= ' ';
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
                $hid = $this->urlPrm;
                if (!is_null($this->name)) {
                    $hid['View']=$this->name;
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
        $this->name=$name;
        $r = $this->buildView(CstMode::V_S_READ, true);
        $r=GenHTML::genFormElem($r, false);
        return $r;
    }

    protected function initMenu()
    {
        $menu = $this->getTopMenu(null);
        if ($menu != []) {
            return;
        }
        $home = [];
        $res = $this->vew->getViewPrm('Home');
        if ($res) {
            $home=$res;
        }
        $selmenu = $this->handle->getSelPath();
        $rmenu=[];
        $res = $this->vew->getViewPrm('MenuExcl');
        if ($res) {
            $rmenu=$res;
        }
        $selmenu= array_diff($selmenu, $rmenu);
        $menu = array_unique(array_merge($home, $selmenu));
        $this->setTopMenu($menu);
    }

    protected function buildView($viewState, $rec)
    {
        $spec=[];
        $specL=[];
        $specS=[];
        $arg = [];

        if (is_null($this->handle) or $this->handle->nullObj()) {
            $topMenu= $this->getTopMenu($viewState);
            $arg[]= [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_TOPMENU,CstView::V_ARG=>$topMenu];
            $speci = [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_OBJ,CstView::V_ARG=>$arg];
            $r=$this->subst($speci, $viewState);
            return $r;
        }
   
        if (!$rec) {
            $this->name=$this->handle->getPrm('View');
        }
        if (is_null($this->name)) {
            $this->name=$this->vew->getDefViewName($this->modName, $viewState);
        }
        
        if ($viewState == CstView::V_S_REF) {
            return $this->elemRefView($this->handle, CstView::V_S_REF);
        }
               
        foreach ($this->getAttrList($viewState) as $attr) {
            $view =[];
            $typ= $this->handle->getTyp($attr);
            if ($typ != Mtype::M_CREF) {
                foreach ($this->getPropList($viewState) as $prop) {
                    $view[] = [CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr, CstView::V_PROP => $prop];
                }
                if ($viewState == CstView::V_S_CREF) {
                    $spec = array_merge($spec, $view);
                } else {
                    $spec[]=[CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_ATTR,CstView::V_ARG=>$view];
                }
            }
            if ($typ == Mtype::M_CREF) {
                $specL[]=$this->buildList($attr, $viewState);
            }
        }
        if ($viewState == CstView::V_S_CREF) {
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
            $specS[]=$this->buildList(CstMode::V_S_SLCT, $viewState);
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
    
    protected function buildList($attr, $viewState)
    {
        $view = [];
        $sortAttr=$this->handle->getPrm(CstView::V_P_SRT);
        if ($sortAttr) {
            $this->urlPrm[CstView::V_P_SRT]=$sortAttr;
            $desc=$this->handle->getPrm(CstView::V_P_DSC);
            if ($desc) {
                $this->urlPrm[CstView::V_P_DSC]=$desc;
            }
        }
        $prm = $this->getAttrHtml($attr, $viewState);
        $view[]=[CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr, CstView::V_PROP => CstView::V_P_LBL];

        $slice = $prm[CstView::V_SLICE];
        $listSize=0;
        if ($slice) {
            if ($viewState==CstMode::V_S_SLCT) {
                $list=$this->handle->select();
            } else {
                $list = $this->handle->getVal($attr);
            }
            $listSize=count($list);
        }
        $ctyp=$prm[CstView::V_CTYP];
        if ($prm[CstView::V_B_NEW] and !$this->handle->isAbstr() and
                ($ctyp == CstView::V_C_TYPN or $listSize==0)) {
            $view[]=[
                    CstView::V_TYPE => CstView::V_CREFMENU,
                    CstView::V_ATTR => $attr,
                    CstView::V_P_VAL=> CstView::V_B_NEW];
        }
        if ($prm[CstView::V_B_SLC] and count($list) >0) {
            $viewElm=[
                    CstView::V_TYPE=> CstView::V_CREFMENU,
                    CstView::V_ATTR => $attr,
                    CstView::V_P_VAL=> CstView::V_B_SLC,
            ];
            if ($ctyp==CstView::V_C_TYP1) {
                $viewElm[CstView::V_P_VAL]= CstView::V_C_TYP1;
                $viewElm[CstView::V_ID]=$list[0];
            }
            $view[]=$viewElm;
        }
        $valList=[];
        if ($ctyp==CstView::V_C_TYPN and $slice > 0) {
            $res = $this->getSlice($attr, $list, $view, $prm);
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
        
    protected function getSlice($attr, array $list, $viewL, $prm)
    {
        $view[]=$viewL[0];
        $listSize=count($list);
        $pos=0;
        $slice = $prm[CstView::V_SLICE];
        $npos = $this->handle->getPrm($attr);
        if (!is_null($npos)) {
            $this->urlPrm[$attr]=$npos;
            $pos=(int) $npos;
            if ($pos<0) {
                $pos=-$pos-$slice;
                if ($pos<0) {
                    $pos=0;
                }
            }
            if ($pos > $listSize) {
                $pos=$listSize-$slice;
            }
        }
        if ($listSize > $slice) {
                $list= array_slice($list, $pos, $slice);
        }
        $viewe=[];
        $first = $prm[CstView::V_CREFLBL];
        foreach ($list as $id) {
            if ($first) {
                $viewe[]=[
                        CstView::V_TYPE=>CstView::V_ELEM,
                        CstView::V_ATTR => $attr,
                        CstView::V_PROP => CstView::V_P_VAL,
                        CstView::V_ID=>$id,
                        CstView::V_CREFLBL=>true];
                $first=false;
            }
            $viewe[]=[
                    CstView::V_TYPE=>CstView::V_ELEM,
                    CstView::V_ATTR => $attr,
                    CstView::V_PROP => CstView::V_P_VAL,
                    CstView::V_ID=>$id
                    
            ];
        }
        if ($prm[CstView::V_COUNTF]) {
            $ind = $listSize;
            if ($listSize > $slice) {
                $nc=count($list)+$pos;
                $ind=$listSize.' : '.$pos.'-'.$nc;
            }
            $view[]=[CstView::V_TYPE=>CstView::V_PLAIN,CstView::V_STRING=>$ind];
        }
        if ($listSize > $slice) {
            $npos= $pos+$slice;
            if ($npos>=$listSize) {
                $npos=$pos;
            }
            $btnList= [CstView::V_B_BGN,cstView::V_B_PRV,CstView::V_B_NXT,CstView::V_B_END];
            $reqBtnList = [];
            foreach ($btnList as $btn) {
                if ($prm[$btn]) {
                    $reqBtnList[]=$btn;
                }
            }
            foreach ($reqBtnList as $btn) {
                switch ($btn) {
                    case CstView::V_B_BGN:
                        $rpos=0;
                        break;
                    case CstView::V_B_PRV:
                        $rpos=-$pos;
                        break;
                    case CstView::V_B_NXT:
                        $rpos=$npos;
                        break;
                    case CstView::V_B_END:
                        $rpos=-$listSize;
                        break;
                }
                $view[]=
                [
                        CstView::V_TYPE=>CstView::V_CREFMENU,
                        CstView::V_ATTR => $attr,
                        CstView::V_P_VAL=>$btn,
                        CstView::V_ID=>$rpos
                        
                ];
            }
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
    
    protected function viewErr()
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
