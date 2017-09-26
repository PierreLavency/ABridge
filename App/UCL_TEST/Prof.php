<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\GenJason;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

class Prof extends CModel 
{

	public function getVal($attr) 
	{
		if ($attr == 'NbrCours') {
			$a = $this->mod->getValN('Donne');
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
		$charge = 'Charge';
		$user ='User';
		$sex='Sexe';
		$country='Country';
		

		$obj = $this->mod;
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);	
		$res = $obj->addAttr('SurName',Mtype::M_STRING);		
		$res = $obj->addAttr('BirthDay',Mtype::M_DATE);		
		$res = $obj->addAttr('Image',Mtype::M_STRING);
		$res = $obj->addAttr('Jason',Mtype::M_TXT);
		$res = $obj->setProp('Jason', Model::P_EVL);
		$res = $obj->setProp('Jason', Model::P_TMP);
		
		if (isset($bindings[$sex])) {
			$sex=$bindings[$sex];
			$res = $obj->addAttr('Sexe',Mtype::M_CODE,"/$sex/Values");		
		
		}
		if (isset($bindings[$country])) {
			$country=$bindings[$country];
			$res = $obj->addAttr('Country',Mtype::M_CODE,"/$country/Values");
		}
		
		if (isset($bindings[$charge])) {
			$charge=$bindings[$charge];
			$res = $obj->addAttr('Donne',Mtype::M_CREF,'/'.$charge.'/Par');
			$obj->addAttr('NbrCours', Mtype::M_INT);
			$obj->setProp('NbrCours', Model::P_EVL);
			$obj->setProp('NbrCours', Model::P_TMP);
			$obj->addAttr('NbrCredits', Mtype::M_INT);
			$obj->setProp('NbrCredits', Model::P_EVL);
		}
		
		if (isset($bindings[$user])) {
			$user = $bindings[$user];
			$obj->addAttr('User',Mtype::M_REF,'/'.$user);
			$res=$obj->setBkey('User',true);
		}
		
	}
	
	
	public function save()
	{
		if ($this->mod->ExistsAttr('Donne')) {
			$credits = 0;
			$list = $this->mod->getValN('Donne');
			foreach ($list as $id) {
				$charge = $this->mod->getCref('Donne',$id);
				$cours = $charge->getRef('De');
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

