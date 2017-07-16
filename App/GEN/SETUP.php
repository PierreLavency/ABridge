<?php
require_once 'CstMode.php';
require_once 'View/CstView.php';
require_once 'Person.php';

class Config
{
	static $config = [
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
}



