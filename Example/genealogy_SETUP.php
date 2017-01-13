<?php
require_once("ViewConstant.php");


	$config = [
	'Handlers' =>
		[
		'CodeValue'  => ['fileBase','genealogy',],
		'Code' 		 => ['fileBase','genealogy',],
		'Student'	 => ['fileBase','genealogy',],
		'Cours'		 => ['fileBase','genealogy',],
		'Inscription'=> ['fileBase','genealogy',],
		'Prof'		 => ['fileBase','genealogy',],
		'Charge'	 => ['fileBase','genealogy',],
		'Person'	 => ['dataBase','genealogy',],
		'ABB'		 => ['dataBase','abb',],
		'Interface'	 => ['dataBase','abb',],
		'Exchange'	 => ['dataBase','abb',],
		],
	'Home' =>
		['Student','Cours','Inscription','Prof','Charge','Person','Code','CodeValue','ABB','Interface','Exchange','Home'],
	'Views' => [
		'ABB'=> [
				'attrList' => [
				V_S_REF		=> ['Name'],
			]
		],
		'Interface'=> [
				'attrList' => [
				V_S_REF		=> ['Name'],
			]
		],
		'Exchange'=> [
				'attrList' => [
				V_S_REF		=> ['Name'],
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
		'Student' => [
			'attrList' => [
				V_S_REF		=> ['SurName','Name'],
			],
			'attrHtml' => [
				V_S_CREA => ['Sexe'=>H_T_RADIO],
				V_S_UPDT => ['Sexe'=>H_T_RADIO],
				V_S_SLCT => ['Sexe'=>H_T_RADIO],
			],
			'attrProp' => [
				V_S_SLCT =>[V_P_LBL,V_P_OP,V_P_VAL],
			],			
			'lblList' => [
				'id'		=> 'Noma',
				'Name' 		=> 'Nom',
				'SurName' 	=> 'Prenom',
				'BirthDay'	=>'Date de naissance',
			],
		],
		'Cours' => [
			'attrList' => [
				V_S_REF		=> ['SurName','Name'],
				V_S_CREF	=> ['id','Credits'],
			]
		],
		'Prof' => [
			'attrList' => [
				V_S_REF		=> ['SurName','Name'],
				V_S_CREF	=> ['id'],
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
		'Inscription' =>[
			'attrHtml' => [
				V_S_CREA => ['A'=>H_T_SELECT,'De'=>H_T_SELECT],
				V_S_UPDT => ['A'=>H_T_SELECT,'De'=>H_T_SELECT],
				V_S_SLCT => ['A'=>H_T_SELECT,'De'=>H_T_SELECT],
			],
		],
		'Charge' =>[
			'attrHtml' => [
				V_S_CREA => ['Par'=>H_T_SELECT,'De'=>H_T_SELECT],
				V_S_UPDT => ['Par'=>H_T_SELECT,'De'=>H_T_SELECT],
				V_S_SLCT => ['Par'=>H_T_SELECT,'De'=>H_T_SELECT],
			],
		],
	]
	];

	
class Cours {

	private $_mod;
	private $_credit;

	function __construct($mod) 
	{
		$this->_mod=$mod;
		$this->_credit=$mod->getVal('Credits');
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
		return true;
	}

	public function afterSave()
	{
		$ncredit = $this->_mod->getVal('Credits');
		if ($ncredit != $this->_credit) {
			$list = $this->_mod->getVal('SuivitPar');
			foreach ($list as $id) {
				$inscription = $this->_mod->getCref('SuivitPar',$id);
				$inscription->save();
			}
		}
		return true;
	}	
}
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
	
class Student {

	private $_mod;

	function __construct($mod) 
	{
		$this->_mod=$mod;
		
	}
	public function getVal($attr) 
	{
		if ($attr == 'NbrCours') {
			$a = $this->_mod->getVal('InscritA');
			$res = count($a);
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
		$credits = 0;
		$list = $this->_mod->getVal('InscritA');
		foreach ($list as $id) {
			$inscription = $this->_mod->getCref('InscritA',$id);
			$cours = $inscription->getRef('A');
			$credit = $cours->getVal('Credits');
			if (!is_null($credit)) {
				$credits = $credits + $credit;
			}
		}
		$this->_mod->setVal('NbrCredits',$credits);
		return true;
	}
	public function afterSave()
	{
		return true;
	}
}

class Inscription {

	private $_mod;
	private $_student;

	function __construct($mod) 
	{
		$this->_mod=$mod;
		
	}
	
	public function delet()
	{
		$this->_student = $this->_mod->getRef('De');
		return true;
	}	

	public function afterDelet() 
	{
		if (!is_null($this->_student)) {
			return ($this->_student ->save());
		}
		return true;
	}

	public function save()
	{

		return true;
	}
	
	public function afterSave() 
	{
		$student = $this->_mod->getRef('De');
		if (! is_null($student)) {
			return ($student->save());
		}
		return true;
	}
}