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

    protected function getAttrList($viewName, $viewState)
    {
        $res=$this->vew->getSpecState($this->modName, $viewName, $viewState, 'attrList');
        if (! is_null($res)) {
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
 
    
    protected function getTopMenu($viewState)
    {
        $selmenu = $this->handle->getSelPath();
        return $this->vew->getTopMenu($viewState, $selmenu);
    }
    
    protected function getMenuObjAction($viewState)
    {
        if ($this->handle->nullObj()) {
            return [];
        }
        return $this->vew->getMenuObjAction($this->modName, $this->name, $viewState);
    }

    protected function getLbl($attr)
    {
        return $this->vew->getLbl($this->modName, $this->name, $attr);
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

    protected function getAttrHtml($attr, $typ, $viewName, $viewState)
    {
        $defList = [
                CstView::V_P_LBL=>true,
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
                CstView::V_P_LBL=>true,
                CstView::V_CTYP=>CstView::V_C_TYP1,
                CstView::V_SLICE=>1,
                CstView::V_B_SLC=>true,
                CstView::V_B_NEW=>true,
        ];
        $resList=[];
        if ($res=$this->vew->getSpecStateAttr($this->modName, $viewName, $viewState, 'attrHtml', $attr)) {
            if (isset($res)) {
                $resList= $res;
            }
        }
        if ($attr == CstMode::V_S_SLCT) { //bof
            return array_merge($defList, $resList);
        }
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
    
    protected function getUpAttrHtml($attr, $typ, $viewState)
    {
        if ($res=$this->vew->getSpecStateAttr($this->modName, $this->name, $viewState, 'attrHtml', $attr)) {
            if (isset($res)) {
                return $res;
            }
        }
        $res=CstHTML::H_T_TEXT;
        if (!Mtype::isStruct($typ)) {
            $res=CstHTML::H_T_TEXTAREA;
        }
        if ($typ == Mtype::M_CODE) {
            $res=CstHTML::H_T_SELECT;
        }
        return $res;
    }
    
    protected function getObjLblList($viewState)
    {
        $result = [];
        if ($this->handle->nullObj()) {
            return $result;
        }

        $lblList = $this->vew->getViewPrm('objLblList');
        if (! $lblList) {
            $lblList=[CstView::V_OBJLBL];
        }
        foreach ($lblList as $lbl) {
            if ($lbl==CstView::V_OBJLBL) {
                $name = $this->vew->getModLbl($this->modName);
            }
            if ($lbl==CstView::V_OBJNME) {
                $name='';
                if ($viewState != CstMode::V_S_CREA and $viewState != CstMode::V_S_SLCT) {
                    $name = $this->getRefLbl($this->handle);
                }
            }
            $result[] = [CstView::V_TYPE=> CstView::V_PLAIN,CstView::V_STRING=>$name,];
        }
        return $result;
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
            $htyp = $this->getUpAttrHtml($attr, $typ, $viewState);
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
                if ($typ == Mtype::M_REF or $typ== Mtype::M_CODE) {
                    foreach ($vals as $v) {
                        $m = $this->handle->getCode($attr, (int) $v);
                        if (! is_null($m)) {
                            $l = $this->getRefLbl($m);
                            $r = [$v,$l];
                            $values[]=$r;
                        }
                    }
                }
                if ($typ==Mtype::M_BOOL) {
                    foreach ($vals as $v) {
                        $l = $this->getLbl($v);
                        $r = [$v,$l];
                        $values[]=$r;
                    }
                }
                
                $res[CstHTML::H_VALUES]=$values;
            }
            return $res ;
        }
      
        if ($viewState == CstMode::V_S_CREA or $viewState==CstMode::V_S_UPDT) {
            // Select ?
            $viewState=CstMode::V_S_READ;
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
            $lbl=false;
            if (isset($spec[CstView::V_CREFLBL]) and  $spec[CstView::V_CREFLBL]) {
                $lbl=true;
            }
            return $this->elemCref($attr, $id, $viewState, $lbl);
        }
        
        if ($typ==Mtype::M_REF) {
            return $this->elemRef($attr, $viewState);
        }
        if ($typ==Mtype::M_CODE) {
            return $this->elemCode($attr, $viewState);
        }

        $attrVal=$this->handle->getVal($attr);
        $htyp = $this->getAttrHtml($attr, $typ, $this->name, $viewState);

        if (is_array($htyp)) {
            $res= $htyp;
        } else {
            $res[CstHTML::H_TYPE] = $htyp;
        }
 
        $res[CstHTML::H_DEFAULT]=$attrVal;
        $res[CstHTML::H_INPUTATTR]='disabled';
        
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
            $row = 1;
            if (isset($tres[CstHTML::H_ROWP])) {
                $row= $tres[CstHTML::H_ROWP]/$height;
            }
            $col = 1;
            if (isset($tres[CstHTML::H_COLP])) {
                $col = $tres[CstHTML::H_COLP]/$width;
            }
            if ($row > $col) {
                $col=$row;
            } else {
                $row=$col;
            }
            $tres[CstHTML::H_ROWP]= round($height * $row);
            $tres[CstHTML::H_COLP]= round($width * $col);
            $res[CstHTML::H_LABEL]=$tres;
        }
        return $res;
    }
    
    protected function elemLink($url, $lbl)
    {
        return [CstHTML::H_TYPE=>CstHTML::H_T_LINK,CstHTML::H_NAME=>$url,CstHTML::H_LABEL=>$lbl];
    }
    
    protected function elemPlain($lbl)
    {
        return [CstHTML::H_TYPE=>CstHTML::H_T_PLAIN,CstHTML::H_DEFAULT=>$lbl];
    }
    
    protected function elementProp($typ, $attr, $prop, $viewState)
    {
        if ($prop == CstView::V_P_OP and $viewState == CstMode::V_S_SLCT) {
            if ($this->handle->isProtected($attr)) {
                return $this->elemPlain('');
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
        return $this->elemPlain($lbl);
    }
    
    protected function elemCode($attr, $viewState)
    {
        $codeHdl=$this->handle->getCodeRef($attr);
        if (is_null($codeHdl)) {
            return $this->elemPlain('');
        }
        $rep=$this->getAttrHtml($attr, Mtype::M_CODE, $this->name, $viewState);
        return $this->elemRefView($codeHdl, $rep);
    }
    
    protected function elemRef($attr, $viewState)
    {
        $refHdl=$this->handle->getRef($attr);
        if (is_null($refHdl)) {
            return $this->elemPlain('');
        }
        $rep=$this->getAttrHtml($attr, Mtype::M_REF, $this->name, $viewState);
        return $this->elemRefView($refHdl, $rep);
    }
        
    protected function elemRefView($refHdl, $rep)
    {
        // can be ref / plain / or an object view
        if ($rep==CstView::V_S_REF) {
            $refpath = $refHdl->getUrl();
            $lbl= $this->getRefLbl($refHdl);
            return $this->elemLink($refpath, $lbl);
        }
        if ($rep==CstHTML::H_T_PLAIN) {
            $lbl= $this->getRefLbl($refHdl);
            return $this->elemPlain($lbl);
        }
        $view= new View($refHdl);
        $lbl=$view->showRec($rep);
        return $this->elemPlain($lbl);
    }
    
    protected function getRefLbl($handle)
    {
        $elmList=[];
        if ($res=$this->vew->getSpecState($handle->getmodName(), null, CstView::V_S_REF, 'attrList')) {
            foreach ($res as $attr) {
                $elmList[]=$handle->getVal($attr);
            }
            $refLbl=implode(' ', $elmList);
            if (!ctype_space($refLbl) and $refLbl!='') {
                return $refLbl;
            }
        }
        return $handle->getid();
    }
    
    protected function elemCref($attr, $id, $viewState, $lbl)
    {
        if ($attr != CstMode::V_S_SLCT) {
            $crefHandle=$this->handle->getCref($attr, $id);
        } else {
            $crefHandle=$this->handle->getObjId($id);
        }
        if (is_null($crefHandle)) {
            return false;
        }
        $v = new View($crefHandle);
        if ($lbl) {
            return $v->getCrefLbls($this->handle, $viewState, $this->urlPrm);
        }
        return $v->buildView(CstView::V_S_CREF, true);
    }
          
    protected function getCrefLbls($handle, $viewState, $prm)
    {
        $lblList= [];
        $attrList=$this->getAttrList($this->name, CstView::V_S_CREF);
        if ($viewState != CstMode::V_S_SLCT) {
            foreach ($attrList as $attr) {
                $lbl=$this->getLbl($attr);
                $lblList[]=$this->elemPlain($lbl);
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
        
    protected function menuObjView($spec, $viewState)
    {
        if ($viewState != CstMode::V_S_READ) {
            return false;
        }
        $viewn=$spec[CstView::V_P_VAL];
        $res= [];
        if ($viewn != $this->name) {
            $lbl=$this->getLbl($viewn);
            $url=$this->handle->getUrl(['View'=>$viewn]);
            return $this->elemLink($url, $lbl);
        }
        $lbl=$this->getLbl($viewn);
        return $this->elemPlain($lbl);
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
        $lbl=$this->getLbl($nav);
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
        return $this->elemLink($path, $lbl);
    }
    
    protected function menuTop($spec, $viewState)
    {
        $res=[];
        $path=$spec[CstView::V_TOPLIST];
        try {
            $hdl = $this->handle->getNewHdl($path);
        } catch (Exception $e) {
            return false;
        }
        $res[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
        $res[CstHTML::H_NAME]=$hdl->getUrl();
        if ($hdl->nullObj()) {
            $mod='Home';
        } else {
            $mod=$hdl->getModName();
        }
        $res[CstHTML::H_LABEL]=$this->vew->getModLbl($mod);
        return $res;
    }
    
    protected function menuCref($spec, $viewState)
    {
        $result=[];
        $nav=$spec[CstView::V_P_VAL];
        $result[CstHTML::H_LABEL]=$this->getLbl($nav);
        $result[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
        $attr=$spec[CstView::V_ATTR];
        $prm = $this->urlPrm;
        if (!is_null($this->name)) {
            $prm[CstView::V_P_VEW]=$this->name;
        }
        switch ($nav) {
            case CstView::V_B_NEW:
                $path=$this->handle->getCrefUrl($attr, CstMode::V_S_CREA, $prm);
                if (is_null($path)) {
                    return false;
                }
                $result[CstHTML::H_NAME]=$path;
                break;
            case CstView::V_B_SLC:
                $path=$this->handle->getCrefUrl($attr, CstMode::V_S_SLCT, $prm);
                if (is_null($path)) {
                    return false;
                }
                $result[CstHTML::H_NAME]=$path;
                break;
            case CstView::V_C_TYP1:
                $id=$spec[CstView::V_ID];
                $crefHandle=$this->handle->getCref($attr, $id);
                if (is_null($crefHandle)) {
                    return false;
                }
                $view = new View($crefHandle);
                $result[CstHTML::H_TYPE]=CstHTML::H_T_LINK;
                $result[CstHTML::H_NAME]=$crefHandle->getUrl([]);
                $result[CstHTML::H_LABEL]= $view->buildView(CstView::V_S_REF, true);
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
                    $result[CstHTML::H_NAME]=$path;
                }
        }
        return $result;
    }
    protected function subst($spec, $viewState)
    {
        $type = $spec[CstView::V_TYPE];
        $result=[];
        switch ($type) {
            case CstView::V_ELEM:
                $result= $this->element($spec, $viewState);
                break;
            case CstView::V_LIST:
                $argSpecList = $spec[CstView::V_ARG];
                if (is_null($argSpecList)) {
                    return false;
                }
                $listTyp=$spec[CstView::V_LT];
                if (isset($spec[CstView::V_ATTR])) {
                    $attr = $spec[CstView::V_ATTR];
                    $aspec = $this->getAttrHtml($attr, Mtype::M_CREF, $this->name, $viewState);
                    $result=$aspec[$listTyp];
                } else {
                    $result= $this->vew->getHtmlList($this->modName, $this->name, $viewState, $listTyp);
                }
                $arg=[];
                $htmlClassElem = $this->vew->getHtmlClassListElem($this->modName, $this->name, $listTyp);
                foreach ($argSpecList as $elem) {
                    $r=$this->subst($elem, $viewState);
                    if ($r) {
                        if ($htmlClassElem) {
                            $r[$htmlClassElem[0]]=$htmlClassElem[1];
                        }
                        $arg[]=$r;
                    }
                }
                $result[CstHTML::H_ARG]=$arg;
                $htmlClass = $this->vew->getHtmlClassList($this->modName, $this->name, $listTyp);
                if ($htmlClass) {
                    $result[$htmlClass[0]]=$htmlClass[1];
                }
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
            case CstView::V_CREFMENU:
                $result= $this->menuCref($spec, $viewState);
                break;
            case CstView::V_FORM:
                $result[CstHTML::H_TYPE]=CstHTML::H_T_FORM;
                $result[CstHTML::H_ACTION]="POST";
                $hid = $this->urlPrm;
                if (!is_null($this->name)) {
                    $hid['View']=$this->name;
                }
                $path = $this->handle->getUrl($hid);
                $result[CstHTML::H_HIDDEN]=['vnum'=>$this->handle->getVal('vnum')];//??
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
        $res = $this->buildView($viewState, false);
        $css = $this->vew->getCssFileName();
        $res=GenHTML::genHTML($res, $css, $show);
        return $res;
    }
  
    protected function showRec($name)
    {
        $this->name=$name;
        $res = $this->buildView(CstMode::V_S_READ, true);
        $res=GenHTML::genFormElem($res, false);
        return $res;
    }
    
    protected function buildView($viewState, $rec)
    {
        $spec=[];
        $specL=[];
        $specS=[];
        $arg = [];
                
        if ($viewState == CstView::V_S_REF) {
            return $this->elemRefView($this->handle, CstView::V_S_REF);
        }
        
        if ($viewState == CstView::V_S_CREF) {
            foreach ($this->getAttrList($this->name, $viewState) as $attr) {
                $spec[]=[
                        CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr, CstView::V_PROP => CstView::V_P_VAL
                        
                ];
            }
            $specf = [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>$viewState,CstView::V_ARG=>$spec];
            return $this->subst($specf, $viewState);
        }
        
        if ($rec) {
            $spec=$this->buildViewObj($this->name, $viewState, $rec);
            return $this->subst($spec, $viewState);
        }
        
        $this->name=$this->handle->getPrm('View');
        if (is_null($this->name)) {
            $this->name=$this->vew->getDefViewName($this->modName, $viewState);
        }
        
        $argList=$this->vew->getTopLists($this->modName, $this->name);
        $speci= [];
        foreach ($argList as $listType) {
            $specElem=[];
            $specElem[CstView::V_TYPE]=CstView::V_LIST;
            $specElem[CstView::V_LT]=$listType;
            switch ($listType) {
                case CstView::V_APPLBLLIST:
                    $specElem[CstView::V_ARG]=$this->vew->getAppLblList($viewState);
                    break;
                case CstView::V_TOPMENU:
                    $specElem[CstView::V_ARG]=$this->getTopMenu($viewState);
                    break;
                case CstView::V_OBJLBLLIST:
                    $specElem[CstView::V_ARG]=$this->getObjLblList($viewState);
                    break;
                case CstView::V_VIEWLIST:
                    $specElem[CstView::V_ARG]=$this->vew->getMenuObjView($this->modName, $viewState);
                    break;
                case CstView::V_OBJACTIONMENU:
                    $specElem[CstView::V_ARG]=$this->getMenuObjAction($viewState);
                    break;
                case CstView::V_ERROR:
                    $specElem[CstView::V_ARG]=$this->viewErr();
                    break;
                case CstView::V_OBJVIEWLIST:
                    $specElem[CstView::V_ARG]=$this->buildObjView($this->name, $viewState);
                    break;
                case CstView::V_CREDITLIST:
                    $specElem[CstView::V_ARG]=$this->vew->getCredit();
                    break;
                default:
                    echo'not found';
            }
            $speci[]=$specElem;
        }

        if ($viewState != CstMode::V_S_READ and ! $this->handle->nullObj()) {
            $speci = [[
                    CstView::V_TYPE=>CstView::V_FORM,
                    CstView::V_ARG=>$speci
                    
            ]];
        }
        $speci =[
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_TOPLIST,
                CstView::V_ARG=>$speci
                
        ];

        return $this->subst($speci, $viewState);
    }
    
    protected function buildObjView($viewName, $viewState)
    {
        $result = [];
        if ($this->handle->nullObj()) {
            return $result;
        }
        $objListView=[CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_OBJLISTVIEW,CstView::V_ARG=>[]];
        if ($viewState==CstMode::V_S_READ) {
            $viewList= $this->vew->getRelViews($this->modName, $viewName, 'before');
            foreach ($viewList as $viewN) {
                $res= $this->buildViewObj($viewN, $viewState, false);
                $objListView[CstView::V_ARG]=$res;
                $result[] = $objListView;
            }
        }
        $objListView[CstView::V_ARG]=$this->buildViewObj($viewName, $viewState, false);
        $result[] = $objListView;
        if ($viewState==CstMode::V_S_READ) {
            $viewList= $this->vew->getRelViews($this->modName, $viewName, 'after');
            foreach ($viewList as $viewN) {
                $res= $this->buildViewObj($viewN, $viewState, false);
                $objListView[CstView::V_ARG]=$res;
                $result[] = $objListView;
            }
        }
        return $result;
    }
    
    protected function buildViewObj($viewName, $viewState, $rec)
    {
        $result=[
                [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_ALIST,CstView::V_ARG=>[]],
                [CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_CLIST,CstView::V_ARG=>null],
        ];
        $spec=[];
        $specL=[];
        foreach ($this->getAttrList($viewName, $viewState) as $attr) {
            $view =[];
            $typ= $this->handle->getTyp($attr);
            if ($typ != Mtype::M_CREF) {
                foreach ($this->vew->getPropList($this->modName, $viewName, $viewState) as $prop) {
                    $view[] = [CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr, CstView::V_PROP => $prop];
                }
                $spec[]=[CstView::V_TYPE=>CstView::V_LIST,CstView::V_LT=>CstView::V_ATTR,CstView::V_ARG=>$view];
            }
            if ($typ == Mtype::M_CREF and $viewState==CstMode::V_S_READ) {
                $specL[]=$this->buildList($attr, $viewName, $viewState);
            }
        }
        $result[0][CstView::V_ARG]=$spec;
        if ($rec) {
            return $result[0];
        }
        if ($viewState == CstMode::V_S_SLCT) {
            $specS[]=$this->buildList(CstMode::V_S_SLCT, $viewName, $viewState);
            $result[1][CstView::V_ARG]=$specS;
        }
        if ($viewState == CstMode::V_S_READ) {
            $result[1][CstView::V_ARG]=$specL;
        }
        return $result;
    }
    
    protected function buildList($attr, $viewName, $viewState)
    {
        $view = [];
        $valList=[];
        $sortAttr=$this->handle->getPrm(CstView::V_P_SRT);
        if ($sortAttr) {
            $this->urlPrm[CstView::V_P_SRT]=$sortAttr;
            $desc=$this->handle->getPrm(CstView::V_P_DSC);
            if ($desc) {
                $this->urlPrm[CstView::V_P_DSC]=$desc;
            }
        }
        $prm = $this->getAttrHtml($attr, Mtype::M_CREF, $viewName, $viewState);
        $slice = $prm[CstView::V_SLICE];
        $ctyp=$prm[CstView::V_CTYP];
        $listSize=0;
        if ($slice) {
            if ($viewState==CstMode::V_S_SLCT) {
                $list=$this->handle->select();
            } else {
                $list = $this->handle->getVal($attr);
            }
            $listSize=count($list);
        }
        if ($ctyp==CstView::V_C_TYPN and $slice > 0) {
            $res = $this->sliceList($attr, $list, $prm);
            $view = $res[0];
            $valList=$res[1];
        }
        if ($prm[CstView::V_P_LBL]) {
            array_unshift(
                $view,
                [CstView::V_TYPE=>CstView::V_ELEM,CstView::V_ATTR => $attr, CstView::V_PROP => CstView::V_P_LBL]
            );
        }
        
        $btnArg=[CstView::V_TYPE=>CstView::V_CREFMENU,CstView::V_ATTR => $attr,];
        if ($prm[CstView::V_B_NEW] and !$this->handle->isAbstr() and
                ($ctyp == CstView::V_C_TYPN or $listSize==0)) {
                    $btnArg[CstView::V_P_VAL]=CstView::V_B_NEW;
                    $view[]=$btnArg;
        }
        if ($prm[CstView::V_B_SLC] and count($list) >0) {
            $btnArg[CstView::V_P_VAL]=CstView::V_B_SLC;
            $viewElm=$btnArg;
            if ($ctyp==CstView::V_C_TYP1) {
                $viewElm[CstView::V_P_VAL]= CstView::V_C_TYP1;
                $viewElm[CstView::V_ID]=$list[0];
            }
            $view[]=$viewElm;
        }
        $specElm= [
                [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_CREF_MLIST,
                CstView::V_ARG=>$view,
                        
                ]
        ];
        if ($valList !=[]) {
            $specElm[]=$valList;
        }
        return [
                CstView::V_TYPE=>CstView::V_LIST,
                CstView::V_LT=>CstView::V_CREF,
                CstView::V_ARG=>$specElm
                
        ];
    }
        
    protected function sliceList($attr, array $list, $prm)
    {
        $view=[];
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
                $endpOS=count($list)+$pos;
                $ind=$listSize.' : '.$pos.'-'.$endpOS;
            }
            $view[]=[CstView::V_TYPE=>CstView::V_PLAIN,CstView::V_STRING=>$ind];
        }
        if ($listSize > $slice) {
            $npos= $pos+$slice;
            if ($npos>=$listSize) {
                $npos=$pos;
            }
            $btnList= [CstView::V_B_BGN,cstView::V_B_PRV,CstView::V_B_NXT,CstView::V_B_END];
            $btnArg=[CstView::V_TYPE=>CstView::V_CREFMENU,CstView::V_ATTR => $attr,];
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
                $btnArg[CstView::V_P_VAL]=$btn;
                $btnArg[CstView::V_ID]=$rpos;
                $view[]=$btnArg;
            }
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
        if ($this->handle->nullObj()) {
            return null;
        }
        $log = $this->handle->getErrLog();
        $logSize=$log->logSize();
        if (!$logSize) {
            return null;
        }
        $result=[];
        for ($i=0; $i<$logSize; $i++) {
            $res= $log->getLine($i);
            $res = CstError::subst($res);
            $result[$i]=[
                    CstView::V_TYPE=> CstView::V_PLAIN,
                    CstView::V_STRING=>$res
            ];
        }
        return $result;
    }
}
