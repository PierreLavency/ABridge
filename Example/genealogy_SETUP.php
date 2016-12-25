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
		'Person' =>
			[
			'attrList' => 
				[
				V_S_SLCT 	=> ['id','Name','SurName'],
				V_S_REF		=> ['SurName','Name'],
				]
			],
		'Student' =>
			[
			'attrList' => 
				[
				V_S_REF		=> ['SurName','Name'],
				]
			],
		'Code' =>
			[
			'attrList' => 
				[
				V_S_REF		=> ['Name'],
				]
			],
		'CodeValue' =>
			[
			'attrList' => 
				[
				V_S_REF		=> ['Name'],
				]
			],
	]
	];

	