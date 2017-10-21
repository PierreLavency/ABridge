<?php
namespace ABridge\ABridge\View;

use ABridge\ABridge\View\View;

use ABridge\ABridge\Comp;

class Vew extends Comp
{
    private static $instance = null;
    private $viewHandler=[]; // mod => spec
    private $isInit=false;
    
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
    
    
    public function getViewPrm($modName)
    {
        if (isset($this->viewHandler[$modName])) {
            return ($this->viewHandler[$modName]);
        }
        return null;
    }
    
    private function setViewPrm($modName, $spec)
    {
        $this->viewHandler[$modName]=$spec;
        return true;
    }
    
    public function init($app, $config)
    {
        $this->isInit=true;
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
  
    public function getViewList($modName, $viewState)
    {
        $specMod=$this->getViewPrm($modName);
        if (is_null($specMod)) {
            return null;
        }
        if ($viewState != CstView::V_S_REF and $viewState != CstView::V_S_CREF and isset($specMod['viewList'])) {
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
    
    public function getSpec($modName, $viewName, $viewState, $specName)
    {
        $specMod=$this->getViewPrm($modName);
        if (is_null($specMod)) {
            return null;
        }
        if ($viewState != CstView::V_S_REF and $viewState != CstView::V_S_CREF and isset($specMod['viewList'])) {
            $viewList=$specMod['viewList'];
            if (isset($viewList[$viewName])) {
                $specView=$viewList[$viewName];
                $res=$this->getSpecElm($specView, $viewState, $specName);
                if ($res) {
                    return $res;
                }
            }
        }
        return $this->getSpecElm($specMod, $viewState, $specName);
    }
    
    private function getSpecElm($specMod, $viewState, $specName)
    {
        if (is_null($specMod)) {
            return null;
        }
        if (isset($specMod[$specName])) {
            $specList=$specMod[$specName];
            if (is_null($viewState)) {
                return $specList;
            }
            if (isset($specList[$viewState])) {
                return $specList[$viewState];
            }
        }
        return null;
    }
}
