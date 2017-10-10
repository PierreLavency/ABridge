<?php
namespace ABridge\ABridge\Adm;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Model;

class Admin extends CModel
{

    public function __construct($mod)
    {
        $this->mod=$mod;
        if (! $mod->existsAttr('Name')) {
            $this->initMod([]);
            $this->mod->saveMod();
        }
    }
    

    public function initMod($bindings)
    {
        $obj = $this->mod;
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res = $obj->addAttr('Parameters', Mtype::M_TXT);
        
        $res = $obj->addAttr('Meta', Mtype::M_BOOL);
        $res = $obj->setProp('Meta', Model::P_TMP);

        $res = $obj->addAttr('Load', Mtype::M_BOOL);
        $res = $obj->setProp('Load', Model::P_TMP);
 
        $res = $obj->addAttr('Delta', Mtype::M_BOOL);
        $res = $obj->setProp('Delta', Model::P_TMP);
        
        $res = $obj->addAttr('Model', Mtype::M_STRING);
        
        $res = $obj->addAttr('MetaData', Mtype::M_TXT);
        $res = $obj->setProp('MetaData', Model::P_EVL);
        $res = $obj->setProp('MetaData', Model::P_TMP);
        
        $res = $obj->addAttr('ModState', Mtype::M_TXT);
        $res = $obj->setProp('ModState', Model::P_EVL);
        $res = $obj->setProp('ModState', Model::P_TMP);
        
        $res = $obj->addAttr('StateHandler', Mtype::M_TXT);
        $res = $obj->setProp('StateHandler', Model::P_EVL);
        $res = $obj->setProp('StateHandler', Model::P_TMP);
        
        return $obj->isErr();
    }
    
    public function getVal($attr)
    {
        if ($attr == 'MetaData') {
            return json_encode($this->mod->getMeta(), JSON_PRETTY_PRINT);
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
            if ($stateHandler) {
                return json_encode($stateHandler->showState($modName), JSON_PRETTY_PRINT);
            }
            return '';
        }
        return $this->mod->getValN($attr);
    }
    
    public function save()
    {
        $attrVal = [];
        
        $app = $this->mod->getVal('Name');
        $path = "App/$app/";
        $prm = $this->mod->getVal('Parameters');
        
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
            try {
                $res = $this->mod->select();
            } catch (\Exception $e) {
                $res=false;
            }
            if (!$res) {
                $this->mod->deleteMod();
                $this->initMod([]);
                $this->mod->saveMod();
                $this->mod->setValN('Name', $app);
                $this->mod->setValN('Parameters', $prm);
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
