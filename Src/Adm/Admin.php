<?php
namespace ABridge\ABridge\Adm;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Mod;

class Admin extends CModel
{

    public function __construct($mod)
    {
        $this->mod=$mod;
        if (! $mod->existsAttr('name')) {
            $this->initMod([]);
            $this->mod->saveMod();
        }
    }
    
    protected $attrList = ['name','path','base','dBase','fileBase','memBase','host','user','pass'];

    public function initMod($bindings)
    {
        $obj = $this->mod;
        
        foreach ($this->attrList as $attr) {
            $res = $obj->addAttr($attr, Mtype::M_STRING);
        }
        
        $res = $obj->addAttr('Meta', Mtype::M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Load', Mtype::M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Delta', Mtype::M_BOOL, M_P_TEMP);
        $res = $obj->addAttr('Model', Mtype::M_STRING);
        $res = $obj->addAttr('MetaData', Mtype::M_TXT, M_P_EVAL);
        $res = $obj->addAttr('ModState', Mtype::M_TXT, M_P_EVAL);
        $res = $obj->addAttr('StateHandler', Mtype::M_TXT, M_P_EVAL);
        
        return $obj->isErr();
    }
    
    public function getVal($attr)
    {
        if ($attr == 'MetaData') {
            return $this->mod->getMeta();
        }
        if ($attr == 'ModState') {
            return  json_encode(Mod::get()->showState(), JSON_PRETTY_PRINT);
        }
        if ($attr=='Model') {
            if (is_null($this->mod->getValN($attr))) {
                return $this->mod->getModName();
            }
        }
        if ($attr == 'StateHandler') {
            $modName=$this->getVal('Model');
            $stateHandler=Mod::get()->getStateHandler($modName);
            return json_encode($stateHandler->showState($modName), JSON_PRETTY_PRINT);
        }
        return $this->mod->getValN($attr);
    }
    
    public function save()
    {
        $attrVal = [];
        foreach ($this->attrList as $attr) {
            $attrVal[$attr] = $this->mod->getVal($attr);
        }
        
        $app = $this->mod->getVal('name');
        $path = "App/$app/";
        
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
        
        if ($this->mod->getId() != 0) {
            $this->mod->setCriteria([], [], []);
            $res = $this->mod->select();
            if (!$res) {
                $this->mod->deleteMod();
                $this->initMod([]);
                $this->mod->saveMod();
                foreach ($attrVal as $attr => $val) {
                    $this->mod->setValN($attr, $val);
                }
                $id=$this->mod->saveN();
                $this->mod->getErrlog()->show();
                return $id;
            }
        }

        return $this->mod->saveN();
    }

    public function delet()
    {
        return false;
    }
}
