<?php

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

class Charge extends CModel
{
	private $prof;
	
    public function initMod($bindings)
    {
        $obj = $this->mod;
        
        $cours = $bindings['Cours'];
        $prof = $bindings['Prof'];
        $res = $obj->addAttr('De', Mtype::M_REF, '/'.$cours);
        $res = $obj->addAttr('Par', Mtype::M_REF, '/'.$prof);
        
        $res = $obj->setMdtr('De', true); // Mdtr
        $res = $obj->setMdtr('Par', true); // Mdtr

        $obj->setCkey(['De','Par'], true);

    }
    
    public function delet()
    {
    	$this->prof = $this->mod->getRef('Par');
    	$res=$this->mod->deletN();
    	if (!is_null($this->prof)) {
    		$this->prof ->save();
    	}
    	return $res;
    }
    
    public function save()
    {
    	$res=$this->mod->saveN();
    	$prof = $this->mod->getRef('Par');
    	if (! is_null($prof)) {
    		$prof->save();
    	}
    	return $res;
    }
}

