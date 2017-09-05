<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

class Inscription extends CModel 
{

	private $_student;
	
	public function initMod($bindings)
	{
		
		$obj = $this->mod;
		
		$student = 'Student';
		$cours='Cours';
		
		$res = $obj->addAttr('De',Mtype::M_REF,'/'.$bindings[$student]);
		$res = $obj->setMdtr('De',true); // Mdtr
		$res = $obj->addAttr('A',Mtype::M_REF,'/'.$bindings[$cours]);
		$res = $obj->setMdtr('A',true); // Mdtr
		$obj->setCkey(['De','A'],true);
		
		
	}
	public function delet()
	{
		$this->_student = $this->mod->getRef('De');
		$res=$this->mod->deletN();
		if (!is_null($this->_student)) {
			$this->_student ->save();
		}
		return $res;
	}
	
	public function save()
	{
		$res=$this->mod->saveN();
		$student = $this->mod->getRef('De');
		if (! is_null($student)) {
			$student->save();
		}
		return $res;
	}

}