<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;


class Config
{
	const DBDEC = 'USR';
	
	const User ='User';
	const Role = 'Role';
	const Session ='Session';
	const Distribution = 'Distribution';
	const Group = 'UserGroup';
	const Adm ='Admin';
	
	static $cmod = [
		self::Session	 	=> 'ABridge\ABridge\Usr\\'.self::Session,
	];

	static $config = [
	'Handlers' =>
		[
		self::User	 	 	=> ['dataBase',self::DBDEC,],
		self::Group		 	=> ['dataBase',self::DBDEC,],		
		self::Role	 	 	=> ['dataBase',self::DBDEC,],
		self::Distribution	=> ['dataBase',self::DBDEC,],
		self::Session	 	=> ['dataBase',self::DBDEC,],
		self::Adm   		=> ['dataBase',self::DBDEC,false],
		],
	'Home' =>
			['/',"/".self::Session."/~","/".self::User."/~","/".self::Adm."/1"],
	'Adm' => [],
	'Usr' => 
		[self::Session=>'BKey'],		
	'Views' => [
			
		self::Adm =>[
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
		self::User =>[
			'lblList' => [
					'Play'			=> 'PlayRoles',
				],
			'attrHtml' => [
					CstMode::V_S_READ => ['Play'=>[CstView::V_SLICE=>15,CstView::V_COUNTF=>false,CstView::V_CTYP=>CstView::V_C_TYPN]],
					CstMode::V_S_UPDT => ['DefaultRole'=>CstHTML::H_T_SELECT],
					CstMode::V_S_SLCT => ['DefaultRole'=>CstHTML::H_T_SELECT],					
				],						
			'attrList' => [
					CstView::V_S_REF		=> ['UserId'],
					CstMode::V_S_SLCT	=> ['UserId',self::Group],
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
						CstMode::V_S_READ	=> ['UserId',self::Group,'DefaultRole','Play'],
						CstMode::V_S_UPDT	=> ['UserId','Password',self::Group,'DefaultRole'],
					],
					'navList' => [
						CstMode::V_S_READ => [CstMode::V_S_UPDT],
					],					
				],
				'Trace' =>[
					'attrList' => [
						CstMode::V_S_READ=> ['id','vnum','ctstp','utstp','MetaData'],
					],
					'navList' => [
						CstMode::V_S_READ => [],
					],
				],
			]				
		],
		self::Group =>[		
			'attrList' => [
				CstView::V_S_REF		=> ['Name'],
				],
		],		
		self::Role =>[	
				'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					CstMode::V_S_UPDT	=> ['JSpec' => [CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_COL=>160,CstHTML::H_ROW=> 30]],
					CstMode::V_S_READ	=> ['JSpec' => [CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_COL=>160,CstHTML::H_ROW=> 30]],
					CstMode::V_S_CREA	=> ['JSpec' => [CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_COL=>160,CstHTML::H_ROW=> 30]],
				]
		],
		self::Session =>[
			'attrList' => [
						CstView::V_S_CREF=> ['id','User','Role','ValidFlag','BKey','vnum','ctstp'],									
			],
			'attrHtml' => [
						CstMode::V_S_UPDT => ['Role'=>CstHTML::H_T_SELECT],
						CstMode::V_S_SLCT => ['Role'=>CstHTML::H_T_SELECT],					
			],		
			'viewList' => [
				'Detail'  => [
					'lblList' => [
						CstMode::V_S_UPDT			=> 'LogIn',
						CstMode::V_S_DELT			=> 'LogOut',	
					],				
					'attrList' => [
						CstMode::V_S_READ=> ['id','User','Role'],
						CstMode::V_S_DELT=> ['id','User','Role'],
						CstMode::V_S_UPDT=> ['id','UserId','Password','Role'],
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
		self::Distribution =>[
			'attrHtml' => [
				CstMode::V_S_CREA => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_UPDT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_SLCT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
			],

		],		
		],
	];		
	
}
	