<?php
require_once("ViewConstant.php");

	$config = [
	'Handlers' =>
		[
		'CodeValue'  => ['dataBase','gen',],
		'Code' 		 => ['dataBase','gen',],
		'Person'	 => ['dataBase','gen',],
		'User'	 	 => ['dataBase','gen',],
		],
	'Home' =>
		['User','Person','Code','CodeValue','Home'],
		
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

class Person {

	private $_mod;

	function __construct($mod) 
	{
		$this->_mod=$mod;
		
	}
	
	public function getVal($attr) 
	{
		if ($attr == 'Age') {
			$a = $this->_mod->getVal('BirthDay');
			if (is_null($a)) {
				return null;
			}
			$b = $this->_mod->getVal('DeathDate');
			if (! is_null($b)) {
				return null;
			}
			$da= date_create($a);
			$db= date_create($b);
			$res = date_diff($da, $db);
			$res = (int) $res->format('%y');
			return $res;

		}
	}
	
	public function delet()
	{
		return true;
	}

	public function afterDelet() 
	{
		return true;
	}
	
	public function save()
	{
			$a = $this->_mod->getVal('BirthDay');
			if (is_null($a)) {
				return true;
			}
			$b = $this->_mod->getVal('DeathDate');
			if (is_null($b)) {
				return true;
			}
			$da= date_create($a);
			$db= date_create($b);
			$res = date_diff($da, $db);
			$res = (int) $res->format('%y');
			$this->_mod->setVal('DeathAge',$res);
			return true;

	}

	public function afterSave()
	{
		return true;
	}	
}
	
