<?php

class Admin extends CModel
{
    public function __construct($mod)
    {
        $this->mod=$mod;
        if (! $mod->existsAttr('Init')) {
            $this->initMod([]);
        }
    }
    
    public function initMod($bindings)
    {
        $obj = $this->mod;
        
        $res = $obj->addAttr('Application', M_STRING);
        $res = $obj->addAttr('Init', M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Meta', M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Load', M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Delta', M_BOOL, M_P_TEMP);
        
        return $obj->isErr();
    }

    public function save()
    {
        $app = $this->mod->getVal('Application');
        $path = "App/$app/";
        
        if ($this->mod->getVal('Init')) {
            $this->mod->deleteMod();
            $this->initMod([]);
            $this->mod->saveMod();
            $this->mod->setVal('Application', $app);
            return $this->mod->saveN();
        }
        if ($this->mod->getVal('Load')) {
            require_once $path.'LOAD.php';
        }
        if ($this->mod->getVal('Meta')) {
            require_once $path.'META.php';
            require_once $path.'LOAD.php';
        }
        if ($this->mod->getVal('Delta')) {
            require_once $path.'DELTA.php';
        }
        
        if (! $this->mod->getId()) {
            $this->mod->setCriteria([], [], []);
            $res = $this->mod->select();
            if (count($res)>0) {
                return $res[0];
            }
        }

        return $this->mod->saveN();
    }

    public function delet()
    {
        return false;
    }
}
