<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\App;

require_once 'Cours.php';
require_once 'Student.php';
require_once 'Inscription.php';   


class Config extends App
{
	const DBDEC = 'genealogy';
	const Session = 'Session';
	const User = 'User';
	const Group = 'UGroup';
	
	public static function  init($prm, $config)
	{
		return $config;
	}
	
	public  static function initMeta($config)
	{
		return true;
	}
	
	public  static function initData($prm=null)
	{
		return  true;
	}
	
	static $config = [
			
	'Default'	=> [
		'base'=>'fileBase',
		'fileBase'=>self::DBDEC,
	],
			
	'Handlers' => [
		'CodeValue'  	=> ['fileBase',self::DBDEC],
		'Code' 			=> ['fileBase',self::DBDEC],
		'Student'	 	=> [],
		'Cours'		 	=> ['fileBase',self::DBDEC],
		'Inscription'	=> ['fileBase',self::DBDEC],
		'Prof'		 	=> ['fileBase',self::DBDEC],
		'Charge'	 	=> ['fileBase',self::DBDEC],
			
		'Session'  	 	=> ['fileBase',self::DBDEC],
		'User'	 	 	=> ['dataBase',self::DBDEC],
		'UGroup'	 	=> ['dataBase',self::DBDEC],		
		'Role'	 	 	=> ['dataBase',self::DBDEC],
		'Distribution'	=> ['dataBase',self::DBDEC],
			
		'Page'		  	=> ['dataBase',self::DBDEC],	
		'Admin'		  	=> ['dataBase',self::DBDEC],	
	],
			
	'Hdl' 	=> [
/*
			'Usr'   => [
					'User'          ,
					'Role'          ,
					'Distribution'  ,
					'Session'       ,
			],
			*/
	],
			
	'Adm'	=> [
			
	],
	
	'View' => [			
		'Home' =>
			['/',
					'/Session/~',
					'/Admin/1',
					'/User/~'
			],
			
		'Admin'	=>[
				'attrList' => [
						CstView::V_S_REF		=> ['id'],
				],
				'lblList'  => [
						CstMode::V_S_UPDT => 'Load',
				],
				'navList' => [
						CstMode::V_S_READ => [CstMode::V_S_UPDT],
				],
		],		
		self::Session =>[
			'attrList' => [
						CstView::V_S_CREF=> ['id','User','ActiveRole','ValidFlag','BKey','vnum','ctstp'],									
			],
			'attrHtml' => [
						CstMode::V_S_UPDT => ['ActiveRole'=>CstHTML::H_T_SELECT],
						CstMode::V_S_SLCT => ['ActiveRole'=>CstHTML::H_T_SELECT],					
			],		
			'viewList' => [
				'Detail'  => [
					'lblList' => [
						CstMode::V_S_UPDT			=> 'LogIn',
						CstMode::V_S_DELT			=> 'LogOut',	
					],				
					'attrList' => [
						CstMode::V_S_READ=> ['id','User','ActiveRole'],
						CstMode::V_S_DELT=> ['id','User','ActiveRole'],
						CstMode::V_S_UPDT=> ['id','UserId','Password','RoleName'],
					],
					
				],
				'Trace' =>[
					'attrList' => [
						CstMode::V_S_READ=> ['id','ValidStart','BKey','vnum','ctstp','utstp'],
					],
					'navList' => [
						CstMode::V_S_READ => [],
					],
				],
			]							

		],
		
		'Distribution' =>[
			'attrHtml' => [
				CstMode::V_S_CREA => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_UPDT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_SLCT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
			],

		],
		self::User =>[
			'lblList' => [
					'Play'			=> 'PlayRoles',
				],
			'attrHtml' => [
					CstMode::V_S_READ 	=> ['Play'=>[CstView::V_SLICE=>15,CstView::V_COUNTF=>false,CstView::V_CTYP=>CstView::V_C_TYPN]],
				],						
			'attrList' => [
					CstView::V_S_REF		=> ['UserId'],
					CstMode::V_S_SLCT	=> ['UserId',self::Group,'DefaultRole'],
				],
			'viewList' => [
				'Password'  => [
					'attrList' => [
						CstMode::V_S_READ	=> ['UserId',],
						CstMode::V_S_CREA	=> ['UserId','NewPassword1','NewPassword2'],
						CstMode::V_S_UPDT	=> ['UserId','Password','NewPassword1','NewPassword2'],
						CstMode::V_S_DELT	=> ['UserId'],		
					],
				],
				'Role'  => [
					'attrList' => [
						CstMode::V_S_READ	=> ['UserId','Profile','ProfProfile',self::Group,'DefaultRole','Play'],
						CstMode::V_S_UPDT	=> ['UserId','Password',self::Group,'DefaultRole'],
					],
					'navList' => [
						CstMode::V_S_READ => [CstMode::V_S_UPDT],
					],					
				],
				'Trace' =>[
					'attrList' => [
						CstMode::V_S_READ=> ['id','vnum','ctstp','utstp'],
					],
					'navList' => [
						CstMode::V_S_READ => [],
					],
				],
			]				
		],
		'UGroup' =>[		
			'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				],
		],		
		'Role' =>[	
				'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				]

		],
		'Student' => [
		
				'attrList' => [
					CstView::V_S_REF		=> ['SurName','Name'],
				],
				'attrHtml' => [
					CstMode::V_S_CREA => ['Sexe'=>CstHTML::H_T_RADIO],
					CstMode::V_S_UPDT => ['Sexe'=>CstHTML::H_T_RADIO],
					CstMode::V_S_SLCT => ['Sexe'=>CstHTML::H_T_RADIO],
					CstMode::V_S_READ => ['InscritA'=>[CstView::V_SLICE=>5,CstView::V_COUNTF=>true,CstView::V_CTYP=>CstView::V_C_TYPN]]	
				],
				'attrProp' => [
					CstMode::V_S_SLCT =>[CstView::V_P_LBL,CstView::V_P_OP,CstView::V_P_VAL],
				],			
				'lblList' => [
					'id'		=> 'Noma',
					'Name' 		=> 'Nom',
					'SurName' 	=> 'Prenom',
					'BirthDay'	=> 'Date de naissance',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','SurName','Name','NbrCours','NbrCredits'],
							CstMode::V_S_UPDT=> ['SurName','Name'],
							CstMode::V_S_CREA=> ['SurName','Name'],
							CstMode::V_S_DELT=> ['SurName','Name'],							
						],
					],
					'Detail'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['SurName','Name','BirthDay','Sexe','Country',],
							CstMode::V_S_UPDT=> ['SurName','Name','BirthDay','Sexe','Country'],
						],
						'navList' => [
							CstMode::V_S_READ => [CstMode::V_S_UPDT],
						],
					],
					'Inscription' =>[
						'attrList' => [
							CstMode::V_S_READ=> ['SurName','Name','InscritA'],
						],
						'navList' => [CstMode::V_S_READ => []
						],	
					],
					'Image' =>[
						'attrProp' => [
							CstMode::V_S_READ =>[CstView::V_P_VAL],
						],				
						'attrList' => [
							CstMode::V_S_READ=> ['Image'],
							CstMode::V_S_UPDT=> ['Image'],
						],
						'attrHtml' => [
							CstMode::V_S_READ => ['Image'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 80]],
						],						
						'navList' => [CstMode::V_S_READ => [CstMode::V_S_UPDT]
						],	
					],						
					'Jason' =>[
						'attrProp' => [
							CstMode::V_S_READ =>[CstView::V_P_VAL],
						],	
						'attrList' => [
							CstMode::V_S_READ=> ['Jason'],
						],
						'attrHtml' => [
							CstMode::V_S_READ => ['Jason'=>[CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_COL=>90,CstHTML::H_ROW=> 40]],
						],					
						'navList' => [CstMode::V_S_READ => []
						],	
					],						
					'Trace' =>[
						'attrList' => [
							CstMode::V_S_READ=> ['id','vnum','ctstp','utstp','User'],
							CstMode::V_S_UPDT=> ['User'],
						],
						'navList' => [CstMode::V_S_READ => [CstMode::V_S_UPDT]
						],
						'attrHtml' => [
							CstMode::V_S_UPDT => ['User'=>CstHTML::H_T_SELECT],
						]
					],					
				],
				
		],
		'Cours' => [

				'attrList' => [
					CstView::V_S_REF		=> ['SurName','Name'],
					CstView::V_S_CREF	=> ['id','Credits','User'],
				],
				'attrHtml' => [
							CstMode::V_S_UPDT => ['User'=>CstHTML::H_T_SELECT],
							CstMode::V_S_SLCT => [CstMode::V_S_SLCT=>[CstView::V_SLICE=>15,CstView::V_COUNTF=>true,CstView::V_CTYP=>CstView::V_C_TYPN]]
				]
				
		],
		'Prof' => [
		
				'attrList' => [
					CstView::V_S_REF		=> ['SurName','Name'],
					CstView::V_S_CREF	=> ['id','User'],
				]
				
		],
		'Code' => [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				]
				
		],
		'CodeValue' =>[
		
				'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				]

		],
		'Inscription' =>[
		
				'attrHtml' => [
					CstMode::V_S_CREA => ['A'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
//					CstMode::V_S_READ => ['A'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
					CstMode::V_S_READ => ['De'=>'Resume'],
					CstMode::V_S_UPDT => ['A'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
					CstMode::V_S_SLCT => ['A'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
				],

		],
		'Charge' =>[
		
				'attrHtml' => [
					CstMode::V_S_CREA => ['Par'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
					CstMode::V_S_UPDT => ['Par'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
					CstMode::V_S_SLCT => ['Par'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
				],
				
		],

	]
	];

}