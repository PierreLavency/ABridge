<?php
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
				V_S_CREA => ['Sexe'=>H_T_RADIO,'A'=>H_T_SELECT,'De'=>H_T_SELECT],
				V_S_UPDT => ['Sexe'=>H_T_RADIO,'A'=>H_T_SELECT,'De'=>H_T_SELECT],
				V_S_SLCT => ['Sexe'=>H_T_RADIO,'A'=>H_T_SELECT,'De'=>H_T_SELECT],
			],
			'attrList' => [
				V_S_SLCT 	=> ['id','Name','SurName','Sexe','Country','BirthDay'],
				V_S_REF		=> ['SurName','Name'],
			]
		],
		'Student' => [
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
	]
	];

	