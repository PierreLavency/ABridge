<?php
namespace ABridge\ABridge\Mod;

abstract class CModel
{

    protected $mod;

    public function __construct($mod)
    {
        $this->mod=$mod;
    }

    public function getMod()
    {
        return $this->mod;
    }
    
    public function initMod($bindings)
    {
        return true;
    }
    
    public function getValues($attr)
    {
        return $this->mod->getValuesN($attr);
    }
    
    public function getVal($attr)
    {
        return $this->mod->getValN($attr);
    }
 

    public function setVal($attr, $val)
    {
        return $this->mod->setValN($attr, $val);
    }
 
    public function delet()
    {
        return $this->mod->deletN();
    }
    
    public function save()
    {
        return $this->mod->saveN();
    }
}
