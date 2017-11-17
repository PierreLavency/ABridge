<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\View\Vew;

abstract class AppComp
{
    protected $config;
    protected $prm=[];
    protected $bindings=[];
    protected $apps = [];
    protected $appName;
    
    
    public function __construct()
    {
        $arg = func_get_args();
        $argN = func_num_args();
        if (method_exists($this, $fct = 'construct'.$argN)) {
            call_user_func_array(array($this, $fct), $arg);
        }
    }
    
    protected function construct1($prm)
    {
        $this->setPrm($prm);
    }
    
    protected function construct2($prm, $bindings)
    {
        $this->bindings=$bindings;
        $this->prm = $prm;
    }
    
    protected function getProp($prop)
    {
        if (isset($this->config[$prop])) {
            return $this->config[$prop];
        }
        return null;
    }
    
    public function setConfig($config)
    {
        $this->config=$config;
    }
    
    
    public function setPrm($ini)
    {
        $appName = $ini['name'];
        $this->appName = $appName;
        // priority : init - spec[Default] - default
        $defaultValues=[
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'base'=>'dataBase',
                'dataBase'=>$appName,
                'memBase'=>$appName,
                'fileBase'=>$appName,
                'host'=>'localhost',
                'user'=>$appName,
                'pass'=>$appName,
                'trace'=>0,
                'config'=>$this,
        ];
        if ($spec=$this->getProp('Default')) {
            $defaultValues=array_merge($defaultValues, $spec);
        }
        $defaultValues=array_merge($defaultValues, $ini);
        $this->prm=$defaultValues;
        return $defaultValues;
    }
    
    public function getPrm()
    {
        return $this->prm;
    }
           
    public function init()
    {
        if (!is_null($spec = $this->getProp('Handlers'))) {
            $result=  Mod::get()->init($this->prm, $spec);
        }
        if (!is_null($spec = $this->getProp('Apps'))) {
            foreach ($spec as $name => $config) {
                $className = 'ABridge\ABridge\Apps\\'.$name;
                $app=new $className($this->prm, $config);
                $this->apps[$name]=$app;
                $app->init();
            }
        }
        if (!is_null($spec = $this->getProp('View'))) {
            Vew::get()->init($this->prm, $spec);
        }
        if (!is_null($spec = $this->getProp('Adm'))) {
            Adm::get()->init($this->prm, $spec);
        }
        if (!is_null($spec = $this->getProp('Hdl'))) {
            Hdl::get()->init($this->prm, $spec);
        }
        if (!is_null($spec = $this->getProp('Log'))) {
            Log::get()->init($this->prm, $spec);
        }
        $this->initOwn();
    }
    
    public function initOwn()
    {
        return true;
    }
       
    public function initMeta()
    {
        $result = [];
        foreach ($this->apps as $name => $app) {
            $result=array_merge($result, $app->initMeta());
        }
        return $this->initOwnMeta($result);
    }
    
    public function initOwnMeta($prm)
    {
        return $prm;
    }
       
    public function initData()
    {
        $result = [];
        foreach ($this->apps as $name => $app) {
            $result=array_merge($result, $app->initData());
        }
        return $this->initOwnData($result);
    }
    
    public function initOwnData($prm)
    {
        return $prm;
    }
    
    public function initDelta()
    {
        return true;
    }
}
