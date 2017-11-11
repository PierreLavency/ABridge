<?php
namespace ABridge\ABridge\View;

use ABridge\ABridge\View\View;

use ABridge\ABridge\Comp;

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
}
