<?php
require_once 'CstMode.php';
require_once 'View/CstView.php';
	
class Config
{
	const DBDEC = 'abbtest';
	
	const ABB = 'ABB';
	const Application = 'Application';
	const Component='Component';
	const Interfaces='Interface';
	const Exchange='Exchange';
	const User ='user';
	
	const ACode = 'ACode';
	const CType='CType';
	const SLevel='SLevel';
	const AStyle='AStyle';
	const SControl='SControl';
	const IType='IType';
	const IUse='IUse';
	
	static $config = [
	'Handlers' =>
		[
		self::ABB		 => ['dataBase',self::DBDEC,false],
		self::Application => ['dataBase',self::DBDEC,],
		self::Component	 => ['dataBase',self::DBDEC,],
		self::Interfaces	 => ['dataBase',self::DBDEC,],
		self::Exchange	 => ['dataBase',self::DBDEC,],
		self::IUse	 	 => ['dataBase',self::DBDEC,],			
		self::IType	 	 => ['dataBase',self::DBDEC,],		
		self::CType	 	 => ['dataBase',self::DBDEC,],
		self::SLevel 	 => ['dataBase',self::DBDEC,],
		self::AStyle 	 => ['dataBase',self::DBDEC,],
		self::SControl 	 => ['dataBase',self::DBDEC,],
		self::ACode	 	 => ['dataBase',self::DBDEC,false],
		self::User	 	 => ['dataBase',self::DBDEC,],
		],
	'Home' =>
		['/',"/".self::Application,"/".self::Component,"/".self::Interfaces,"/".self::Exchange],
	'Views' => [
		self::Application=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					V_S_CREA => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_UPDT => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_SLCT => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_READ => ['Authenticity'=>H_T_PLAIN,'Confidentiality'=>H_T_PLAIN,'Availability'=>H_T_PLAIN,'Integrity'=>H_T_PLAIN,'Style'=>H_T_PLAIN,],					V_S_CREF => ['Authenticity'=>H_T_PLAIN,'Confidentiality'=>H_T_PLAIN,'Availability'=>H_T_PLAIN,'Integrity'=>H_T_PLAIN,'Style'=>H_T_PLAIN,],
				],
				'lblList'  => [
					'In' => 'Interfaces', 'Out' => 'Exchanges', 'BuiltFrom' => 'Components',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Owner','Alias','Style'],
							V_S_UPDT=> ['id','Name','CodeNm','Owner','Alias','Style',],
							V_S_CREA=> ['id','Name','CodeNm','Owner','Alias','Style',],
							V_S_DELT=> ['id','Name','CodeNm','Owner','Alias','Style',],							
						],
					],
					'Decription'  => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','ShortDesc','LongDesc'],
							V_S_UPDT=> ['id','ShortDesc','LongDesc'],							
						],
						'navList' => [V_S_READ => [V_S_UPDT],
						],
					],
					'Components' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','BuiltFrom'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Interface' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','In'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Exchanges' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Out'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Security'  => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Authenticity','Availability','Confidentiality','Integrity'],
							V_S_UPDT=> ['id','Authenticity','Availability','Confidentiality','Integrity'],							
						],
					    'navList' => [V_S_READ => [V_S_UPDT],
						],
					],					
				]
				
		],			
		self::Component=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					V_S_CREA => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_UPDT => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_SLCT => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_READ => ['CType'=>H_T_PLAIN,'SourceControl'=>H_T_PLAIN],
					V_S_CREF => ['CType'=>H_T_PLAIN,'SourceControl'=>H_T_PLAIN],
				],
				'lblList'  => [
					'CType' => 'Type', 'Of' => 'Application ', 'In' => 'Interfaces', 'Out' => 'Exchanges',
				],	
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							V_S_READ=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							V_S_UPDT=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							V_S_CREA=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							V_S_DELT=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],							
						],	1
					],						
					'Interface' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','In'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Exchanges' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Out'],							
						],
						'navList' => [V_S_READ => []
						],
					],				
				],
			],	
		self::Interfaces=> [
		
				'attrList' => [
	//				V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					V_S_CREA => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_UPDT => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_SLCT => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_READ => ['IType'=>H_T_PLAIN,'IUse'=>H_T_PLAIN],
					V_S_CREF => ['IType'=>H_T_PLAIN,'IUse'=>H_T_PLAIN],
				],
				'lblList'  => [
					'IType' => 'Type', 'Of' => 'Application ', 'IUse' => 'Usage', 'UsedBy' => 'Exchanges',
				],	
				
		],
		self::Exchange=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				]
				
		],
		self::CType=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		self::SLevel=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		self::AStyle=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		self::SControl=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		self::IType=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		self::IUse=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],				
		],
	];		
	
}