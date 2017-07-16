<?php
require_once 'CstMode.php';
require_once 'View/CstView.php';

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
						V_S_REF		=> ['id'],
				],
				'lblList'  => [
						V_S_UPDT => 'Load',
				],
				'navList' => [
						V_S_READ => [V_S_UPDT],
				],
		],		
		self::User =>[
			'lblList' => [
					'Play'			=> 'PlayRoles',
				],
			'attrHtml' => [
					V_S_READ => ['Play'=>[H_SLICE=>15,V_COUNTF=>false,V_CTYP=>V_C_TYPN]],
					V_S_UPDT => ['DefaultRole'=>H_T_SELECT],
					V_S_SLCT => ['DefaultRole'=>H_T_SELECT],					
				],						
			'attrList' => [
					V_S_REF		=> ['UserId'],
					V_S_SLCT	=> ['UserId',self::Group],
				],
			'viewList' => [
				'Password'  => [
					'attrList' => [
						V_S_READ	=> ['UserId',],
						V_S_CREA	=> ['UserId','NewPassword1','NewPassword2'],
						V_S_UPDT	=> ['UserId','Password','NewPassword1','NewPassword2'],
						V_S_DELT	=> ['UserId'],		
					],
				],
				'Role'  => [
					'attrList' => [
						V_S_READ	=> ['UserId',self::Group,'DefaultRole','Play'],
						V_S_UPDT	=> ['UserId','Password',self::Group,'DefaultRole'],
					],
					'navList' => [
						V_S_READ => [V_S_UPDT],
					],					
				],
				'Trace' =>[
					'attrList' => [
						V_S_READ=> ['id','vnum','ctstp','utstp','MetaData'],
					],
					'navList' => [
						V_S_READ => [],
					],
				],
			]				
		],
		self::Group =>[		
			'attrList' => [
				V_S_REF		=> ['Name'],
				],
		],		
		self::Role =>[	
				'attrList' => [
					V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					V_S_UPDT	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
					V_S_READ	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
					V_S_CREA	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
				]
		],
		self::Session =>[
			'attrList' => [
						V_S_CREF=> ['id','User','Role','ValidFlag','BKey','vnum','ctstp'],									
			],
			'attrHtml' => [
						V_S_UPDT => ['Role'=>H_T_SELECT],
						V_S_SLCT => ['Role'=>H_T_SELECT],					
			],		
			'viewList' => [
				'Detail'  => [
					'lblList' => [
						V_S_UPDT			=> 'LogIn',
						V_S_DELT			=> 'LogOut',	
					],				
					'attrList' => [
						V_S_READ=> ['id','User','Role'],
						V_S_DELT=> ['id','User','Role'],
						V_S_UPDT=> ['id','UserId','Password','Role'],
					],
					
				],
				'Trace' =>[
					'attrList' => [
						V_S_READ=> ['id','ValidStart','BKey','vnum','ctstp','utstp'],
					],
					'navList' => [
						V_S_READ => [],
					],
				],
			]							

		],
		self::Distribution =>[
			'attrHtml' => [
				V_S_CREA => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_UPDT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_SLCT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
			],

		],		
		],
	];		
	
}
	