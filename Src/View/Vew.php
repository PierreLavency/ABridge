<?php
namespace ABridge\ABridge\View;

use ABridge\ABridge\View\View;

use ABridge\ABridge\Comp;

class Vew extends Comp
{
    private static $instance = null;
    private $viewHandler=[]; // mod => spec
    
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
    
    public function begin($show, $handle)
    {
        $v=new View($handle);
        $action = $handle->getAction();
        $v->show($action, $show);
        return true;
    }
    
    public function isNew()
    {
        return false;
    }
    
    public function initMeta($appPrm, $config)
    {
        return true;
    }
}
