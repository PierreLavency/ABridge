<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\GenJason;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

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
	
	
	public function initMod($bindings)
	{
		$obj = $this->mod;
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);
		
		$res = $obj->addAttr('SurName',Mtype::M_STRING);
		
		$res = $obj->addAttr('BirthDay',Mtype::M_DATE);
		
		
		$res = $obj->addAttr('Sexe',Mtype::M_CODE,'/'.$Code."/$sex_id/Values");
		
		
		$res = $obj->addAttr('Country',Mtype::M_CODE,'/'.$Code."/$country_id/Values");
		
		
		$res = $obj->addAttr('InscritA',Mtype::M_CREF,'/'.$Inscription.'/De');
		
		$obj->addAttr('NbrCours',Mtype::M_INT,M_P_EVAL);
		$obj->addAttr('NbrCredits',Mtype::M_INT,M_P_EVALP);
		$obj->addAttr('Jason',Mtype::M_TXT,M_P_EVAL);
		$obj->addAttr('Image',Mtype::M_STRING);
		
		$obj->addAttr($User,Mtype::M_REF,'/'.$User);
		$res=$obj->setBkey($User,true);
		
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

