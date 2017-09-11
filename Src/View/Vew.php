<?php
namespace ABridge\ABridge\View;

use ABridge\ABridge\View\View;
use ABridge\ABridge\Handler;
use ABridge\ABridge\Comp;

class Vew extends Comp
{
    private static $instance = null;
    
    private function __construct()
    {
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Vew();
        }
        return self::$instance;
    }
    
    public function reset()
    {
        $this->isNew=false;
        self::$instance =null;
        return true;
    }
    
    public function init($app, $config)
    {
        foreach ($config as $mod => $modConf) {
            if ($mod != 'Home' and $mod !='MenuExcl' and $mod !='modLblList') {
                $speci = Handler::get()->getViewHandler($mod);
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
                Handler::get()->setViewHandler($mod, $speci);
            } else {
                Handler::get()->setViewHandler($mod, $modConf);
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
