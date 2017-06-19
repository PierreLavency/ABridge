<?php
require_once 'CstMode.php';

	$config = [
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
	'Home' =>
		['/',],
	'Views' => [
		'Album'=> [
		
				'attrList' => [
					V_S_REF	=> ['Nom'],
				],			
				'lblList'  => [
				],
				'viewList' => [
					'Photos'  => [
						'attrList' => [
							V_S_READ=> ['Photos',],
						],
						'navList' => [
							V_S_READ => [],
						],
						'attrHtml' => [
							V_S_READ => ['Photos'=>[H_SLICE=>4,V_COUNTF=>true,V_CTYP=>V_C_TYPN,
										 V_CVAL=>[H_TYPE=>H_T_NTABLE,H_TABLEN=>2]]],
						],							
					],
					'Descritpion'  => [
						'attrList' => [
							V_S_READ=> ['Nom','Description'],
							V_S_UPDT=> ['id','Nom','Description'],
							V_S_CREA=> ['id','Nom','Description'],
							V_S_DELT=> ['id','Nom','Description'],							
						],
					],					
				]	
		],
		'Photo'=> [		
				'attrList' => [			
					V_S_CREF	=> ['id','Photo'],					
				],
				'attrHtml' => [
					V_S_READ => ['Photo'=>[H_TYPE=>H_T_IMG,H_ROWP=> 600,H_COLP=> 400]],
					V_S_CREF => ['Photo'=>[H_TYPE=>H_T_IMG,H_ROWP=> 300,H_COLP=> 100]],

				],	
				'lblList'  => [
				
				],	
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
	
