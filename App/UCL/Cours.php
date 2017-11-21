<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

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
		if ($this->mod->existsAttr('SuiviPar')) {
			$ncredit = $this->mod->getValN('Credits');
			if ($ncredit != $this->_credit) {
				$list = $this->mod->getValN('SuiviPar');
				foreach ($list as $id) {
					$inscription = $this->mod->getCref('SuiviPar',$id);
					$inscription->save();
				}
			}
		}
		return $res;
	}

	public function initMod($bindings)
	{
		$inscription = 'Inscription';
		$user ='User';
		$charge='Charge';
		$ugroup='UserGroup';
			
		$obj = $this->mod;
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);
		$obj->addAttr('Credits',Mtype::M_INT);	
		
		if (isset($bindings[$inscription])) {
			$inscription=$bindings[$inscription];
			$res = $obj->addAttr('SuiviPar',Mtype::M_CREF,'/'.$inscription.'/A');
		}
		
		if (isset($bindings[$charge])) {
			$charge=$bindings[$charge];
			$obj->addAttr('Par',Mtype::M_CREF,"/".$charge."/De");
		}
		
		if (isset($bindings[$user])) {
			$user = $bindings[$user];
			$obj->addAttr('User',Mtype::M_REF,'/'.$user);
		}
		
		if (isset($bindings[$ugroup])) {
			$ugroup = $bindings[$ugroup];
			$obj->addAttr('UserGroup',Mtype::M_REF,'/'.$ugroup);
		}
	}
	
}
	
