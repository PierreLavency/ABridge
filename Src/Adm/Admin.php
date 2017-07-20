<?php
namespace ABridge\ABridge\Adm;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

class Admin extends CModel
{
    public function __construct($mod)
    {
        $this->mod=$mod;
        if (! $mod->existsAttr('Application')) {
            $this->initMod([]);
            $this->mod->saveMod();
        }
    }
    
    public function initMod($bindings)
    {
        $obj = $this->mod;
        
        $res = $obj->addAttr('Application', Mtype::M_STRING);
        $res = $obj->addAttr('Init', Mtype::M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Meta', Mtype::M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Load', Mtype::M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Delta', Mtype::M_BOOL, M_P_TEMP);
        
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
