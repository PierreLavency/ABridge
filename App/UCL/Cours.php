<?php
use ABridge\ABridge\Mod\CModel;

class Cours extends CModel 
{

	private $_credit=0;

	function __construct($mod) 
	{
		$this->mod=$mod;
		if ($mod->getId()) {
			$this->_credit=$mod->getValN('Credits');
		}
	}
		
	public function save()
	{
		$res=$this->mod->saveN();
		$ncredit = $this->mod->getValN('Credits');
		if ($ncredit != $this->_credit) {
			$list = $this->mod->getValN('SuivitPar');
			foreach ($list as $id) {
				$inscription = $this->mod->getCref('SuivitPar',$id);
				$inscription->save();
			}
		}
		return $res;
	}	
}
	
