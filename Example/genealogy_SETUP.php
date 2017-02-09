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
		'User'	 	 => ['dataBase','genealogy',],
		],
	'Home' =>
		['User','Student','Cours','Inscription','Prof','Charge','Code','CodeValue','Home'],
		
	'Views' => [
		'User' =>[
		
				'attrList' => [
					V_S_REF		=> ['SurName','Name'],
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
					'BirthDay'	=> 'Date de naissance',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							V_S_READ=> ['id','SurName','Name','NbrCours','NbrCredits'],
							V_S_UPDT=> ['SurName','Name'],
							V_S_CREA=> ['SurName','Name'],
							V_S_DELT=> ['SurName','Name'],							
						],
					],
					'Detail'  => [
						'attrList' => [
							V_S_READ=> ['SurName','Name','BirthDay','Sexe','Country'],
							V_S_UPDT=> ['SurName','Name','BirthDay','Sexe','Country'],
						],
						'navList' => [V_S_READ => [V_S_UPDT],
						],
					],
					'Inscription' =>[
						'attrList' => [
							V_S_READ=> ['SurName','Name','InscritA'],
						],
						'navList' => [
						],	
					],						
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