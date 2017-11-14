<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Hdl\Hdl;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\View\Vew;

abstract class AppCom
{
    protected $config;
    protected $prm;
    protected $bindings;
    protected $apps = [];
    
    public function __construct($prm, $bindings)
    {
        $this->prm=$prm;
        $this->bindings=$bindings;
    }
    
    protected function getProp($prop)
    {
        if (isset($config[$prop])) {
            return $config[$prop];
        }
        return null;
    }
        
    protected function init()
    {
        if ($spec=$this->getProp('Handlers')) {
            $result=  Mod::get()->init($this->prm, $spec);
        }
        if ($spec=$this->getProp('Apps')) {
            foreach ($spec as $name => $config) {
                $className = 'ABridge\ABridge\Apps\\'.$name;
                $app=new $className($this->prm, $config);
                $this->apps[$name]=$app;
                $app->init();
            }
        }
        if ($spec=$this->getProp('View')) {
            Vew::get()->init($this->prm, $spec);
        }
        if ($spec=$this->getProp('Adm')) {
            Adm::get()->init($this->prm, $spec);
        }
        if ($spec=$this->getProp('Hdl')) {
            Hdl::get()->init($this->prm, $spec);
        }
        if ($spec=$this->getProp('Log')) {
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
        foreach ($this->apps as $app) {
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
        foreach ($this->apps as $app) {
            $result=array_merge($result, $app->initData());
        }
        $this->initOwnData($result);
        return true;
    }
    
    public function initOwnData($prm)
    {
        return $prm;
    }
}
