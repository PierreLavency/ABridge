<?php
require_once 'CstMode.php';
require_once 'CstView.php';


	$config = [
	'Handlers' =>
		[
		'Recette'		=> ['dataBase',],
		'Ingredient' 	=> ['dataBase',],
		'UniteMesure'	=> ['dataBase',],
		'UniteTemps'	=> ['dataBase',],
		'TypeRecette'	=> ['dataBase',],
		'NiveauDifficulte' =>['dataBase',],
		'AbstractCode'	=> ['dataBase',],	
		'User'	 	 	=> ['dataBase',],
		'Role'	 	 	=> ['dataBase',],
		'Distribution'	=> ['dataBase',],
		'Session'		=> ['dataBase',],		
		],
	'Home' =>
		['/Recette','/','/UniteMesure','/UniteTemps','/TypeRecette','/NiveauDifficulte','/User','/Role','/Distribution'],
	'Views' => [
		'Recette'=> [
		
				'attrList' => [
					V_S_REF		=> ['Nom'],
					V_S_CREF	=> ['id','TypeRecette','NiveauDifficulte','Minutes'],
				],
				'attrHtml' => [

				],
				'lblList'  => [
					'NiveauDifficulte'=>'Niveau de difficulte:',
					'TypeRecette' => 'Type de recette:',
					'Nom'=>'Nom de la recette:',
					'New'=>'Ajouter',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							V_S_READ=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							V_S_UPDT=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							V_S_CREA=> ['Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							V_S_DELT=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],							
						],
						'attrHtml' => [
							V_S_READ => ['Photo'=>[H_TYPE=>H_T_IMG,H_ROWP=> 80],0],
						],	
					],
					'Description'  => [
						'attrList' => [
							V_S_READ=> ['Nom','Ingredients','Description',],
							V_S_UPDT=> ['Nom','Description',],							
						],
						'attrProp' => [
							V_S_READ =>[V_P_VAL],
						],		
						'attrHtml' => [
							V_S_READ => ['Ingredients'=>[H_SLICE=>20,V_COUNTF=>false,V_CTYP=>V_C_TYPN]]
						],	
						'navList' => [V_S_READ => [V_S_UPDT],
						],
					],
					'Ingredients' => [
						'attrList' => [
							V_S_READ=> ['Nom','Ingredients'],							
						],
						'navList' => [V_S_READ => []
						],
					],
				]
				
		],
		
		'Ingredient'=> [
		
				'attrList' => [
//					V_S_REF		=> ['Nom'],
				],
				'attrHtml' => [
				],
				'lblList'  => [
				],	
		],	

		'UniteMesure'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'TypeRecette'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'NiveauDifficulte'=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		'User' =>[		
			'attrList' => [
				V_S_REF		=> ['SurName','Name'],
				],
		],
		'Role' =>[	
				'attrList' => [
					V_S_REF		=> ['Name'],
				]

		],
		'Distribution' =>[
			'attrHtml' => [
				V_S_CREA => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_UPDT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_SLCT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
			],

		],
		],
	];		
	
