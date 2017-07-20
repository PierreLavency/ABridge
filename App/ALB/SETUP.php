<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;


class Config
{
	
	static $config = [
	'Handlers' =>
		[
		'Album'		=> ['dataBase',],
		'Photo' 	=> ['dataBase',],
		'AbstractCode'	=> ['dataBase',],	
		'User'	 	 	=> ['dataBase',],
		'Role'	 	 	=> ['dataBase',],
		'Distribution'	=> ['dataBase',],
		'Session'		=> ['dataBase',],		
		],
	'View' => [
		'Home' =>
			['/',],
		'Album'=> [
		
				'attrList' => [
					CstView::V_S_REF	=> ['Nom'],
				],			
				'lblList'  => [
				],
				'viewList' => [
					'Photos'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['Photos',],
						],
						'navList' => [
							CstMode::V_S_READ => [],
						],
						'attrHtml' => [
								CstMode::V_S_READ => [
										'Photos'=>[
												CstView::V_SLICE=>4,
												CstView::V_COUNTF=>true,
												CstView::V_CTYP=>CstView::V_C_TYPN,
												CstView::V_CVAL=>[CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,CstHTML::H_TABLEN=>2]
												
										]],
						],							
					],
					'Descritpion'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['Nom','Description'],
							CstMode::V_S_UPDT=> ['id','Nom','Description'],
							CstMode::V_S_CREA=> ['id','Nom','Description'],
							CstMode::V_S_DELT=> ['id','Nom','Description'],							
						],
					],					
				]	
		],
		'Photo'=> [		
				'attrList' => [			
					CstView::V_S_CREF	=> ['id','Photo'],					
				],
				'attrHtml' => [
					CstMode::V_S_READ => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 600,CstHTML::H_COLP=> 400]],
					CstView::V_S_CREF => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 300,CstHTML::H_COLP=> 100]],

				],	
				'lblList'  => [
				
				],	
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