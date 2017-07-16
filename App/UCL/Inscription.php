<?php
use ABridge\ABridge\CModel;

class Inscription extends CModel 
{

	private $_student;
	
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