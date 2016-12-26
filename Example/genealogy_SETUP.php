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
		'Person'	 => ['dataBase','genealogy',],
		],
	'Home' =>
		['Student','Cours','Person','Code','CodeValue','Home'],
	'Views' => [
		'Person' => [
			'attrHtml' => [
				V_S_CREA => ['Sexe'=>H_T_RADIO],
				V_S_UPDT => ['Sexe'=>H_T_RADIO],
				V_S_SLCT => ['Sexe'=>H_T_RADIO],
			],
			'attrList' => [
//				V_S_SLCT 	=> ['id','Name','SurName','Sexe','Country','BirthDay'],
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
				$a = date(M_FORMAT_T);
			}
			$b = $this->_mod->getVal('DeathDate');
			if (is_null($b)) {
				$b = date(M_FORMAT_T);
			}
			$da= date_create($a);
			$db= date_create($b);
			$res = date_diff($da, $db);
			$res = (int) $res->format('%y');
			return $res;

		}
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
		if ($attr == 'CreditNumber') {
			$a = $this->_mod->getVal('InscritA');
			$res = count($a);
			return $res;
		}
	}	
}	
	