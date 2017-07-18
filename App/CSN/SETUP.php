<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;


class Config
{
	

	static $config = [
	'Handlers' =>
		[
		'Recette'		=> ['dataBase',],
		'Ingredient' 	=> ['dataBase',],
		'UniteMesure'	=> ['dataBase',],
		'UniteTemps'	=> ['dataBase',],
		'TypeRecette'	=> ['dataBase',],
		'NiveauDifficulte' =>['dataBase',],
		'AbstractCode'	=> ['dataBase','CSN',false],	
		'User'	 	 	=> ['dataBase',],
		'Role'	 	 	=> ['dataBase',],
		'Distribution'	=> ['dataBase',],
		'Session'		=> ['dataBase',],		
		],
	'Home' =>
		['/',],
	'Views' => [
		'Recette'=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Nom'],
					CstView::V_S_CREF	=> ['id','TypeRecette','NiveauDifficulte','Minutes'],
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
							CstMode::V_S_READ=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							CstMode::V_S_UPDT=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							CstMode::V_S_CREA=> ['Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							CstMode::V_S_DELT=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],							
						],
						'attrHtml' => [
							CstMode::V_S_READ => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 80],0],
						],	
					],
					'Description'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['Nom','Ingredients','Description',],
							CstMode::V_S_UPDT=> ['Nom','Description',],							
						],
						'attrProp' => [
								CstMode::V_S_READ =>[CstView::V_P_VAL],
						],		
						'attrHtml' => [
								CstMode::V_S_READ => ['Ingredients'=>[CstView::V_SLICE=>20,CstView::V_COUNTF=>false,CstView::V_CTYP=>CstView::V_C_TYPN]]
						],	
						'navList' => [CstMode::V_S_READ => [CstMode::V_S_UPDT],
						],
					],
					'Ingredients' => [
						'attrList' => [
							CstMode::V_S_READ=> ['Nom','Ingredients'],							
						],
						'navList' => [CstMode::V_S_READ => []
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
					CstView::V_S_REF		=> ['Value'],
				]
				
		],
		'TypeRecette'=> [
		
				'attrList' => [
						CstView::V_S_REF		=> ['Value'],
				]
				
		],
		'NiveauDifficulte'=> [
		
				'attrList' => [
						CstView::V_S_REF		=> ['Value'],
				]
				
		],
		'User' =>[		
			'attrList' => [
					CstView::V_S_REF		=> ['SurName','Name'],
				],
		],
		'Role' =>[	
				'attrList' => [
						CstView::V_S_REF		=> ['Name'],
				]

		],
		'Distribution' =>[
			'attrHtml' => [
				CstMode::V_S_CREA => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_UPDT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_SLCT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
			],

		],
		],
	];		
	
}