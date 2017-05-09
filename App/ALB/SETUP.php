<?php
require_once 'CstMode.php';
require_once 'CstView.php';


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
		['/','/Album','/Photo','/User','/Role','/Distribution'],
	'Views' => [
		'Album'=> [
		
				'attrList' => [
					V_S_REF	=> ['Nom'],
				],
				'attrHtml' => [
					V_S_READ => ['Photos'=>[H_SLICE=>1,V_COUNTF=>true,V_CTYP=>V_C_TYPN]],
				],				
				'lblList'  => [

				],
				'viewList' => [
					'Photos'  => [
						'attrList' => [
							V_S_READ=> ['Photos',],
						],
						'navList' => [V_S_READ => [],
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
					V_S_READ => ['Photo'=>[H_TYPE=>H_T_IMG,H_ROWP=> 100,H_COLP=> 200]],
					V_S_CREF => ['Photo'=>[H_TYPE=>H_T_IMG,H_ROWP=> 100,H_COLP=> 200]],

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
	
