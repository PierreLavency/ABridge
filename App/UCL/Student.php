<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\GenJason;

class Student extends CModel 
{

	public function getVal($attr) 
	{
		if ($attr == 'NbrCours') {
			$a = $this->mod->getValN('InscritA');
			$res = count($a);
			return $res;
		}
		if ($attr == 'Jason') {
			$res = GenJason::genJASON($this->mod,false,true);
			return $res;
		}
		return $this->mod->getValN($attr);
	}
	
	public function save()
	{
		$credits = 0;
		$list = $this->mod->getValN('InscritA');
		foreach ($list as $id) {
			$inscription = $this->mod->getCref('InscritA',$id);
			$cours = $inscription->getRef('A');
			$credit = $cours->getVal('Credits');
			if (!is_null($credit)) {
				$credits = $credits + $credit;
			}
		}
		$this->mod->setVal('NbrCredits',$credits);
		return $this->mod->saveN();
	}
}

