<?php
require_once("ViewConstant.php");


	$config = [
	'Handlers' =>
		[
		'ABB'		 => ['dataBase','abbtest',],
		'Application'=> ['dataBase','abbtest',],
		'Component'	 => ['dataBase','abbtest',],
		'Interface'	 => ['dataBase','abbtest',],
		'Exchange'	 => ['dataBase','abbtest',],
		'IUse'	 	 => ['dataBase','abbtest',],			
		'IType'	 	 => ['dataBase','abbtest',],		
		'CType'	 	 => ['dataBase','abbtest',],
		'SLevel' 	 => ['dataBase','abbtest',],
		'AStyle' 	 => ['dataBase','abbtest',],
		'SControl' 	 => ['dataBase','abbtest',],
		'ACode'	 	 => ['dataBase','abbtest',],
		'User'	 	 => ['dataBase','abbtest',],
		],
	'Home' =>
		['Application','Component','Interface','Exchange','Home'],
	'Views' => [
		'Application'=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					V_S_CREA => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_UPDT => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_SLCT => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_READ => ['Authenticity'=>H_T_PLAIN,'Confidentiality'=>H_T_PLAIN,'Availability'=>H_T_PLAIN,'Integrity'=>H_T_PLAIN,'Style'=>H_T_PLAIN,],
					V_S_CREF => ['Authenticity'=>H_T_PLAIN,'Confidentiality'=>H_T_PLAIN,'Availability'=>H_T_PLAIN,'Integrity'=>H_T_PLAIN,'Style'=>H_T_PLAIN,],
				],
				'lblList'  => [
					'In' => 'Interfaces', 'Out' => 'Exchanges', 'BuiltFrom' => 'Components',
				],
				
		],			
		'Component'=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					V_S_CREA => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_UPDT => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_SLCT => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_READ => ['CType'=>H_T_PLAIN,'SourceControl'=>H_T_PLAIN],
					V_S_CREF => ['CType'=>H_T_PLAIN,'SourceControl'=>H_T_PLAIN],
				],
				'lblList'  => [
					'CType' => 'Type', 'Of' => 'Application ', 'In' => 'Interfaces', 'Out' => 'Exchanges',
				],	
				
		],
		'Interface'=> [
		
				'attrList' => [
	//				V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					V_S_CREA => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_UPDT => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_SLCT => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_READ => ['IType'=>H_T_PLAIN,'IUse'=>H_T_PLAIN],
					V_S_CREF => ['IType'=>H_T_PLAIN,'IUse'=>H_T_PLAIN],
				],
				'lblList'  => [
					'IType' => 'Type', 'Of' => 'Application ', 'IUse' => 'Usage', 'UsedBy' => 'Exchanges',
				],	
				
		],
		'Exchange'=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				]
				
		],
		'CType'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'SLevel'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'AStyle'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'SControl'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'IType'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'IUse'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],				
		],
	];		
	
