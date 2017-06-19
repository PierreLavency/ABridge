<?php
require_once 'CstMode.php';
require_once '/View/Src/CstView.php';
require_once 'CModel.php';

	$config = [
	'Handlers' =>
		[
		'Person'	 => ['dataBase'],
		'User'	 	 => ['dataBase'],
		'CodeValue'  => ['dataBase'],
		'Code' 		 => ['dataBase'],		
		],
	'Home' =>
		['/',],
		
	'Views' => [
		'User' =>[
		
				'attrList' => [
					V_S_REF		=> ['SurName','Name'],
					]
					
			],
		'Person' => [
		
				'attrHtml' => [
					V_S_CREA => ['Sexe'=>H_T_RADIO],
					V_S_UPDT => ['Sexe'=>H_T_RADIO],
					V_S_SLCT => ['Sexe'=>H_T_RADIO],
					V_S_READ => ['text'=>[H_TYPE=>H_T_TEXTAREA,H_COL=>90,H_ROW=> 10]],
				],
				'attrList' => [
					V_S_CREF 	=> ['id','Sexe','BirthDay','DeathDate','Age','DeathAge','Father','Mother'],
					V_S_REF		=> ['SurName','Name'],
				],
				'attrProp' => [
					V_S_SLCT =>[V_P_LBL,V_P_OP,V_P_VAL],
				]
				
		],
		'Code' => [
		
				'attrList' => [
					V_S_REF		=> ['Name'],
				]
				
		],
		'CodeValue' =>[
		
				'attrList' => [
					V_S_REF		=> ['Name'],
				]
				
		],
	]
	];

class Person extends CModel
{

	public function getVal($attr) 
	{
		if ($attr == 'Age') {
			$a = $this->mod->getValN('BirthDay');
			if (is_null($a)) {
				return null;
			}
			$b = $this->mod->getValN('DeathDate');
			if (! is_null($b)) {
				return null;
			}
			$da= date_create($a);
			$db= date_create($b);
			$res = date_diff($da, $db);
			$res = (int) $res->format('%y');
			return $res;
		} else {
			return $this->mod->getValN($attr);
		}
	}
	
	public function save()
	{
			$a = $this->mod->getValN('BirthDay');
			$b = $this->mod->getValN('DeathDate');
			if (!is_null($b) and ! is_null($a)) {				
				$da= date_create($a);
				$db= date_create($b);
				$res = date_diff($da, $db);
				$res = (int) $res->format('%y');
				$this->mod->setValN('DeathAge',$res);
			}
			return $this->mod->saveN();
	}

}
	
