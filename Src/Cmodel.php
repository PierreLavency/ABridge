<?php
abstract class CModel
{

    protected $mod;

    public function __construct($mod)
    {
        $this->mod=$mod;
    }
    
    public function getVal($attr)
    {
        return $this->mod->getValN($attr);
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
