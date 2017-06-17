<?php
require_once 'CstMode.php';
require_once 'CstView.php';
require_once 'CModel.php';
require_once '/User/Src/User.php';
require_once '/User/Src/Session.php';
require_once '/User/Src/Role.php';
require_once '/User/Src/Distribution.php'; 
	
	require_once 'CLASSDEC.php';

	$config = [
	'Handlers' =>
		[
		$User	 	 => ['dataBase',$DBDEC,],
		$Group		 => ['dataBase',$DBDEC,],		
		$Role	 	 => ['dataBase',$DBDEC,],
		$Distribution=> ['dataBase',$DBDEC,],
		$Session	 => ['dataBase',$DBDEC,],
		],
	'Home' =>
		['/',"/$Session/~","/$User/~",],
	'Session' => 
		[$Session=>'BKey'],		
	'Views' => [

		$User =>[
			'lblList' => [
					'Play'			=> 'PlayRoles',
				],
			'attrHtml' => [
					V_S_READ 	=> ['Play'=>[H_SLICE=>15,V_COUNTF=>false,V_CTYP=>V_C_TYPN]],
				],						
			'attrList' => [
					V_S_REF		=> ['UserId'],
					V_S_SLCT	=> ['UserId',$Group,'DefaultRole'],
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
						V_S_READ	=> ['UserId',$Group,'DefaultRole','Play'],
						V_S_UPDT	=> ['UserId','Password',$Group,'DefaultRole'],
					],
					'navList' => [
						V_S_READ => [V_S_UPDT],
					],					
				],
				'Trace' =>[
					'attrList' => [
						V_S_READ=> ['id','vnum','ctstp','utstp'],
					],
					'navList' => [
						V_S_READ => [],
					],
				],
			]				
		],
		$Group =>[		
			'attrList' => [
				V_S_REF		=> ['Name'],
				],
		],		
		$Role =>[	
				'attrList' => [
					V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					V_S_UPDT	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
					V_S_READ	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
					V_S_CREA	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
				]
		],
		$Session =>[
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
		$Distribution =>[
			'attrHtml' => [
				V_S_CREA => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_UPDT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_SLCT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
			],

		],		
		],
	];		
	

	