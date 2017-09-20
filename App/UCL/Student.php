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
		$Code = 'Code';	
		$sex_id=1;
		$country_id=2;
		$inscription = 'Inscription';
		$user ='User';
		$sex='Sex';
		$country='Country';
		

		$obj = $this->mod;
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);	
		$res = $obj->addAttr('SurName',Mtype::M_STRING);		
		$res = $obj->addAttr('BirthDay',Mtype::M_DATE);		
		$res = $obj->addAttr('Image',Mtype::M_STRING);
		$res = $obj->addAttr('Jason',Mtype::M_TXT,M_P_EVAL);
		
		if (isset($bindings[$sex])) {
			$sex=$bindings[$sex];
			$res = $obj->addAttr('Sexe',Mtype::M_CODE,"/$sex/Values");		
		
		}
		if (isset($bindings[$country])) {
			$res = $obj->addAttr('Country',Mtype::M_CODE,"/$country/Values");
		}
		
		if (isset($bindings[$inscription])) {
			$inscription=$bindings[$inscription];
			$res = $obj->addAttr('InscritA',M_CREF,'/'.$inscription.'/De');
			$obj->addAttr('NbrCours',Mtype::M_INT,M_P_EVAL);
			$obj->addAttr('NbrCredits',Mtype::M_INT,M_P_EVALP);
			$obj->addAttr('Jason',Mtype::M_TXT,M_P_EVAL);
		}
		
		if (isset($bindings[$user])) {
			$user = $bindings[$user];
			$obj->addAttr($user,Mtype::M_REF,'/'.$user);
			$res=$obj->setBkey($user,true);
		}
		
	}
	
	
	public function save()
	{
		if ($this->mod->ExistsAttr('InscritA')) {
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
		}
		return $this->mod->saveN();
	}
}

