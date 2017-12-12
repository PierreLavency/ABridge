<?php
namespace ABridge\ABridge\View;

use ABridge\ABridge\View\View;
use ABridge\ABridge\Comp;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\Hdl\CstMode;

class Vew extends Comp
{
    private static $instance = null;
    private $viewHandler=[]; // mod => spec
    private $isInit=false;
    private $appPrm;
    
    private function __construct()
    {
        $this->isNew=false;
        $this->viewHandler=[];
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Vew();
        }
        return self::$instance;
    }
    
    public static function reset()
    {
        self::$instance =null;
        return true;
    }
    
    
    public function getViewPrm($prmName)
    {
        if (isset($this->viewHandler[$prmName])) {
            return ($this->viewHandler[$prmName]);
        }
        return null;
    }
    
    private function setViewPrm($prmName, $spec)
    {
        $this->viewHandler[$prmName]=$spec;
        return true;
    }
    
    public function init($app, $config)
    {
        $this->isInit=true;
        $this->appPrm=$app;
        foreach ($config as $mod => $modConf) {
            if ($mod != 'Home' and $mod !='MenuExcl' and $mod !='modLblList') {
                $speci = $this->getViewPrm($mod);
                if ($speci) {
                    $speciV = [];
                    if (isset($speci['viewList'])) {
                        $speciV= $speci['viewList'];
                    }
                    $specmV = [];
                    if (isset($modConf['viewList'])) {
                        $specmV= $modConf['viewList'];
                    }
                    $speci['viewList']=array_merge($speciV, $specmV);
                } else {
                    $speci=$modConf;
                }
                $this->setViewPrm($mod, $speci);
            } else {
                $this->setViewPrm($mod, $modConf);
            }
        }
    }
    
    public function getAppName()
    {
        return $this->appPrm['name'];
    }
    
    
    public function getCredit()
    {
        return [[CstView::V_TYPE=>CstView::V_PLAIN,CstView::V_STRING=>'ABridge']];
    }
    
    public function begin($prm)
    {
        list($show,$handle)=$prm;
        $view=new View($handle);
        $action = $handle->getAction();
        $view->show($action, $show);
        return true;
    }
    
    public function isNew()
    {
        return false;
    }
    
    public function initMeta()
    {
        return [];
    }
    
    public function getAppLblList($viewState)
    {
        $name=$this->getModLbl($this->getAppName());
        $res = [CstView::V_TYPE=> CstView::V_PLAIN,CstView::V_STRING=>$name,];
        return [$res];
    }
    
    public function getModLbl($mod)
    {
        $lblList = $this->getViewPrm('modLblList');
        if ($lblList and isset($lblList[$mod])) {
            return $lblList[$mod];
        }
        return $mod;
    }
    
    public function getTopMenu($viewState, $selmenu)
    {
        $topMenu=[];
        $home = $this->getHome();
        $rmenu=$this->getExcl();
        $selmenu= array_diff($selmenu, $rmenu);
        $menu = array_unique(array_merge($home, $selmenu));
        foreach ($menu as $path) {
            $topMenu[]=
            [CstView::V_TYPE=>CstView::V_TOPMENU,CstView::V_TOPLIST=>$path];
        }
        return $topMenu;
    }
    
    public function getHome()
    {
        $res = $this->getViewPrm('Home');
        if ($res) {
            return $res;
        }
        return [];
    }
    
    public function getExcl()
    {
        $res = $this->getViewPrm('MenuExcl');
        if ($res) {
            return $res;
        }
        return [];
    }
    
    public function getMenuObjView($modName, $viewState)
    {
        if ($res = $this->getViewList($modName, $viewState)) {
            $navView=[];
            foreach ($res as $viewN) {
                $navView[]=[CstView::V_TYPE=>CstView::V_OBJVIEWMENU,CstView::V_P_VAL=>$viewN];
            }
            return $navView;
        }
        return [];
    }
    
    public function getViewList($modName, $viewState)
    {
        $specMod=$this->getViewPrm($modName);
        if (is_null($specMod)) {
            return null;
        }
        if (isset($specMod['viewListMenu'])) {
            return $specMod['viewListMenu'];
        }
        if (isset($specMod['viewList'])) {
            $viewList=$specMod['viewList'];
            return array_keys($viewList);
        }
        return null;
    }
    
    public function getDefViewName($modName, $viewState)
    {
        $viewList = $this->getViewList($modName, $viewState);
        if ($viewList) {
            return $viewList[0];
        }
        return null;
    }
    
    public function getHtmlList($modName, $viewName, $viewState, $listType)
    {
        $htmlList = [
                CstView::V_CREDITLIST    => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_OBJLBLLIST    => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_OBJVIEWLIST   => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_OBJLISTVIEW   => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_VIEWLIST      => [CstHTML::H_TYPE =>CstHTML::H_T_1TABLE],
                CstView::V_TOPLIST       => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_APPLBLLIST    => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_TOPMENU       => [CstHTML::H_TYPE =>CstHTML::H_T_1TABLE],
                CstView::V_OBJACTIONMENU => [CstHTML::H_TYPE =>CstHTML::H_T_1TABLE],
                CstView::V_ALIST         => [CstHTML::H_TYPE =>CstHTML::H_T_TABLE],
                CstView::V_ATTR          => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_CLIST         => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_CREFLBL       => [CstHTML::H_TYPE =>CstHTML::H_T_TABELBL],
                CstView::V_CREF          => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR],
                CstView::V_CREF_MLIST    => [CstHTML::H_TYPE =>CstHTML::H_T_1TABLE],
                CstView::V_CVAL          => [CstHTML::H_TYPE =>CstHTML::H_T_TABLE],
                CstView::V_ERROR         => [CstHTML::H_TYPE =>CstHTML::H_T_LIST],
                CstView::V_S_CREF        => [CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR], // caller or callee ?
                
                
        ];
        $res=$this->getSpecStateAttr($modName, $viewName, $viewState, 'listHtml', $listType);
        if (!is_null($res)) {
            return $res;
        }
        return $htmlList[$listType];
    }
    
    public function getSpecStateAttr($modName, $viewName, $viewState, $specName, $attr)
    {
        if ($viewState != CstView::V_S_REF and $viewState != CstView::V_S_CREF) {
            if (isset($this->viewHandler[$modName]['viewList'][$viewName][$specName][$viewState][$attr])) {
                return $this->viewHandler[$modName]['viewList'][$viewName][$specName][$viewState][$attr];
            }
        }
        if (isset($this->viewHandler[$modName][$specName][$viewState][$attr])) {
            return $this->viewHandler[$modName][$specName][$viewState][$attr];
        }
        if (isset($this->viewHandler[$specName][$viewState][$attr])) {
            return $this->viewHandler[$specName][$viewState][$attr];
        }
        return null;
    }
    
    public function getPropList($modName, $viewName, $viewState)
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
        
        if ($res=$this->getSpecState($modName, $viewName, $viewState, 'attrProp')) {
            return $res;
        }
        return $attrProp[$viewState];
    }
    
    public function getMenuObjAction($modName, $viewName, $viewState)
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
        $res=$this->getSpecState($modName, $viewName, $viewState, 'navList');
        if (!is_null($res)) {
            $navList=[];
            foreach ($res as $navE) {
                $navList[]= [CstView::V_TYPE=>CstView::V_OBJACTIONMENU,CstView::V_P_VAL=>$navE];
            }
            return $navList;
        }
        return $nav[$viewState];
    }
    public function getSpecState($modName, $viewName, $viewState, $specName)
    {
        if ($viewState != CstView::V_S_REF and $viewState != CstView::V_S_CREF) {
            if (isset($this->viewHandler[$modName]['viewList'][$viewName][$specName][$viewState])) {
                return $this->viewHandler[$modName]['viewList'][$viewName][$specName][$viewState];
            }
        }
        if (isset($this->viewHandler[$modName][$specName][$viewState])) {
            return $this->viewHandler[$modName][$specName][$viewState];
        }
        if (isset($this->viewHandler[$specName][$viewState])) {
            return $this->viewHandler[$specName][$viewState];
        }
        return null;
    }

    
    public function getHtmlClassList($modName, $viewName, $listType)
    {
        return $this->getSpecAttr($modName, $viewName, 'listHtmlClass', $listType);
    }
    
    public function getHtmlClassListElem($modName, $viewName, $listType)
    {
        return $this->getSpecAttr($modName, $viewName, 'listHtmlClassElem', $listType);
    }
    public function getLbl($modName, $viewName, $attr)
    {
        $res= $this->getSpecAttr($modName, $viewName, 'lblList', $attr);
        if (!is_null($res)) {
            return $res;
        }
        return $attr;
    }
    
    protected function getSpecAttr($modName, $viewName, $specName, $attr)
    {
        
        if (isset($this->viewHandler[$modName]['viewList'][$viewName][$specName][$attr])) {
            return $this->viewHandler[$modName]['viewList'][$viewName][$specName][$attr];
        }
        
        if (isset($this->viewHandler[$modName][$specName][$attr])) {
            return $this->viewHandler[$modName][$specName][$attr];
        }
        if (isset($this->viewHandler[$specName][$attr])) {
            return $this->viewHandler[$specName][$attr];
        }
        return null;
    }
    
    public function getRelViews($modName, $viewName, $rel)
    {
        $res=$this->getSpec($modName, $viewName, $rel.'Views');
        if (!is_null($res)) {
            return $res;
        }
        return [];
    }
    
    public function getTopLists($modName, $viewName)
    {
        $res=$this->getSpec($modName, $viewName, 'topList');
        if (!is_null($res)) {
            return $res;
        }
        $argList=[
                CstView::V_APPLBLLIST,
                CstView::V_TOPMENU,
                CstView::V_OBJLBLLIST,
                CstView::V_VIEWLIST,
                CstView::V_OBJACTIONMENU,
                CstView::V_ERROR,
                CstView::V_OBJVIEWLIST,
        ];
        return $argList;
    }
    public function getSpec($modName, $viewName, $specName)
    {

        if (isset($this->viewHandler[$modName]['viewList'][$viewName][$specName])) {
            return $this->viewHandler[$modName]['viewList'][$viewName][$specName];
        }

        if (isset($this->viewHandler[$modName][$specName])) {
            return $this->viewHandler[$modName][$specName];
        }
        if (isset($this->viewHandler[$specName])) {
            return $this->viewHandler[$specName];
        }
        return null;
    }
    
    public function getCssFileName()
    {
        if (isset($this->appPrm['cssName'])) {
            $cssName = $this->appPrm['cssName'];
            $fpath= $this->appPrm['fpath'];
            $cssfileName = $fpath . $cssName;
            if (file_exists($cssfileName)) {
                $file = file_get_contents($cssfileName, FILE_USE_INCLUDE_PATH);
                return $file;
            }
        }
        return null;
    }
}
