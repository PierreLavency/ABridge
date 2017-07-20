<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;


class Config 
{
	
	const DBDEC = 'abb';
	
	const ABB = 'ABB';
	const Application = 'Application';
	const Component='Component';
	const Interfaces ='Interface';
	const Exchange='Exchange';
	
	const ACode = 'ACode';
	const CType='CType';
	const SLevel='SLevel';
	const AStyle='AStyle';
	const SControl='SControl';
	const IType='IType';
	const IUse='IUse';
	
	const User ='User';
	const Role = 'Role';
	const Session ='Session';
	const Distribution = 'Distribution';
	const Group = 'UserGroup';
	
	const Adm ='Admin';


	static $config = [
	'Handlers' =>
		[
		self::ABB		 => 	['dataBase',self::DBDEC],
		self::Application=> 	['dataBase',self::DBDEC],
		self::Component	 => 	['dataBase',self::DBDEC],
		self::Interfaces => 	['dataBase',self::DBDEC],
		self::Exchange	 => 	['dataBase',self::DBDEC],
		self::IUse	 	 => 	['dataBase',self::DBDEC],			
		self::IType	 	 => 	['dataBase',self::DBDEC],		
		self::CType	 	 =>		['dataBase',self::DBDEC],
		self::SLevel 	 => 	['dataBase',self::DBDEC],
		self::AStyle 	 =>		['dataBase',self::DBDEC],
		self::SControl 	 => 	['dataBase',self::DBDEC],
		self::ACode	 	 => 	['dataBase',self::DBDEC],
		self::User	 	 => 	['dataBase',self::DBDEC],
		self::Group		 => 	['dataBase',self::DBDEC],		
		self::Role	 	 => 	['dataBase',self::DBDEC],
		self::Distribution=> 	['dataBase',self::DBDEC],
		self::Session	 => 	['dataBase',self::DBDEC],
		self::Adm	     => 	['dataBase',self::DBDEC],
		],

	'Adm' => [
			
	],
	'Usr' => [
			
	],		
	'View' => [			
		'Home' =>
			['/','/'.self::Session.'/~','/'.self::User.'/~',],
		'MenuExcl' =>
			["/".self::ABB],			
		self::Application=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					CstMode::V_S_CREA => ['Authenticity'=>CstHTML::H_T_SELECT,'Confidentiality'=>CstHTML::H_T_SELECT,'Availability'=>CstHTML::H_T_SELECT,'Integrity'=>CstHTML::H_T_SELECT,'Style'=>CstHTML::H_T_SELECT,],
					CstMode::V_S_UPDT => ['Authenticity'=>CstHTML::H_T_SELECT,'Confidentiality'=>CstHTML::H_T_SELECT,'Availability'=>CstHTML::H_T_SELECT,'Integrity'=>CstHTML::H_T_SELECT,'Style'=>CstHTML::H_T_SELECT,],
					CstMode::V_S_SLCT => ['Authenticity'=>CstHTML::H_T_SELECT,'Confidentiality'=>CstHTML::H_T_SELECT,'Availability'=>CstHTML::H_T_SELECT,'Integrity'=>CstHTML::H_T_SELECT,'Style'=>CstHTML::H_T_SELECT,],
					CstMode::V_S_READ => ['Authenticity'=>CstHTML::H_T_PLAIN,'Confidentiality'=>CstHTML::H_T_PLAIN,'Availability'=>CstHTML::H_T_PLAIN,'Integrity'=>CstHTML::H_T_PLAIN,'Style'=>CstHTML::H_T_PLAIN,],					CstView::V_S_CREF => ['Authenticity'=>CstHTML::H_T_PLAIN,'Confidentiality'=>CstHTML::H_T_PLAIN,'Availability'=>CstHTML::H_T_PLAIN,'Integrity'=>CstHTML::H_T_PLAIN,'Style'=>CstHTML::H_T_PLAIN,],
				],
				'lblList'  => [
					'In' => 'Interfaces', 'Out' => 'Exchanges', 'BuiltFrom' => 'Components',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','Owner','Alias','Style'],
							CstMode::V_S_UPDT=> ['id','Name','CodeNm','Owner','Alias','Style',],
							CstMode::V_S_CREA=> ['id','Name','CodeNm','Owner','Alias','Style',],
							CstMode::V_S_DELT=> ['id','Name','CodeNm','Owner','Alias','Style',],							
						],
					],
					'Decription'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','ShortDesc','LongDesc'],
							CstMode::V_S_UPDT=> ['id','ShortDesc','LongDesc'],							
						],
						'navList' => [CstMode::V_S_READ => [CstMode::V_S_UPDT],
						],
					],
					'Components' => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','BuiltFrom'],							
						],
						'navList' => [CstMode::V_S_READ => []
						],
					],
					'Interface' => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','In'],							
						],
						'navList' => [CstMode::V_S_READ => []
						],
					],
					'Exchanges' => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','Out'],							
						],
						'navList' => [CstMode::V_S_READ => []
						],
					],
					'Security'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','Authenticity','Availability','Confidentiality','Integrity'],
							CstMode::V_S_UPDT=> ['id','Authenticity','Availability','Confidentiality','Integrity'],							
						],
					    'navList' => [CstMode::V_S_READ => [CstMode::V_S_UPDT],
						],
					],					
				]
				
		],			
		self::Component=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					CstMode::V_S_CREA => ['CType'=>CstHTML::H_T_SELECT,'SourceControl'=>CstHTML::H_T_SELECT],
					CstMode::V_S_UPDT => ['CType'=>CstHTML::H_T_SELECT,'SourceControl'=>CstHTML::H_T_SELECT],
					CstMode::V_S_SLCT => ['CType'=>CstHTML::H_T_SELECT,'SourceControl'=>CstHTML::H_T_SELECT],
					CstMode::V_S_READ => ['CType'=>CstHTML::H_T_PLAIN,'SourceControl'=>CstHTML::H_T_PLAIN],
					CstView::V_S_CREF => ['CType'=>CstHTML::H_T_PLAIN,'SourceControl'=>CstHTML::H_T_PLAIN],
				],
				'lblList'  => [
					'CType' => 'Type', 'Of' => 'Application ', 'In' => 'Interfaces', 'Out' => 'Exchanges',
				],	
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							CstMode::V_S_UPDT=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							CstMode::V_S_CREA=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							CstMode::V_S_DELT=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],							
						],	1
					],						
					'Interface' => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','In'],							
						],
						'navList' => [CstMode::V_S_READ => []
						],
					],
					'Exchanges' => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Name','CodeNm','Out'],							
						],
						'navList' => [CstMode::V_S_READ => []
						],
					],				
				],
			],	
		self::Interfaces=> [
		
				'attrList' => [
	//				CstView::V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					CstMode::V_S_CREA => ['IType'=>CstHTML::H_T_SELECT,'IUse'=>CstHTML::H_T_SELECT],
					CstMode::V_S_UPDT => ['IType'=>CstHTML::H_T_SELECT,'IUse'=>CstHTML::H_T_SELECT],
					CstMode::V_S_SLCT => ['IType'=>CstHTML::H_T_SELECT,'IUse'=>CstHTML::H_T_SELECT],
					CstMode::V_S_READ => ['IType'=>CstHTML::H_T_PLAIN,'IUse'=>CstHTML::H_T_PLAIN],
					CstView::V_S_CREF => ['IType'=>CstHTML::H_T_PLAIN,'IUse'=>CstHTML::H_T_PLAIN],
				],
				'lblList'  => [
					'IType' => 'Type', 'Of' => 'Application ', 'IUse' => 'Usage', 'UsedBy' => 'Exchanges',
				],	
				
		],
		self::Exchange=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['CodeNm'],
				]
				
		],
		self::CType=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Value'],
				]
				
		],
		self::SLevel=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Value'],
				]
				
		],
		self::AStyle=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Value'],
				]
				
		],
		self::SControl=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Value'],
				]
				
		],
		self::IType=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Value'],
				]
				
		],
		self::IUse=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Value'],
				]
				
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
						CstMode::V_S_READ	=> ['UserId',self::Group,'DefaultRole','Play'],
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
	