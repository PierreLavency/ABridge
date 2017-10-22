<?php

use ABridge\ABridge\App;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Apps\Cda;
use ABridge\ABridge\Apps\UsrApp;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

class Config extends App
{
	
	const DBDEC = 'abb';
	
	const ABB = 'ABB';
	const Application = 'Application';
	const Component='Component';
	const Interfaces ='Interface';
	const Exchange='Exchange';
	
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
	
	public static function initMeta($config)
	{
		AdmApp::initMeta(self::$config['Apps']['AdmApp']);
		UsrApp::initMeta(self::$config['Apps']['UsrApp']);
		Cda::initMeta(self::$config['Apps']['Cda']);
		self::loadMeta();
		return true;
	}
	
	
	public static function initData($prm)
	{		
		UsrApp::initData(self::$config['Apps']['UsrApp']);	
		AdmApp::initData(self::$config['Apps']['AdmApp']);
		Cda::initData(self::$config['Apps']['Cda']);
		self::loadRole();
		return true;
	}
	
	public static function  init($prm, $config)
	{
		return $config;
	}
	
	static $config = [
			
	'Default'	=> [
			'dataBase'=>self::DBDEC,
	],
			
	'Apps'	=>[
			'UsrApp'=>[],
			'AdmApp'=>[],
			'Cda'=>[
					Cda::CODELIST=>[self::IUse,self::IType,self::CType,self::SLevel,self::AStyle,self::SControl],
					cda::CODEDATA=>[
							self::CType=>['Message','Batch','GUI'],
							self::SLevel=>['Critical','Major','Minor','None'],
							self::AStyle=>['Recoverable Transactions','Low Latency, High Capacity','Data Warehousing, Reporting & Analytics','Data Broadcast & Streaming','GUI Applications'],
							self::SControl=>['Internal','External'],
							self::IType=>['Message','Notification','Batch','DataBase'],
							self::IUse=>['Internal','External'],
					],
			],
	],
			
	'Handlers' =>
		[
		self::ABB		 => 	[],
		self::Application=> 	['dataBase',self::DBDEC],
		self::Component	 => 	['dataBase',self::DBDEC],
		self::Interfaces => 	['dataBase',self::DBDEC],
		self::Exchange	 => 	['dataBase',self::DBDEC],
		],

				
	'View' => [			
		'Home' =>
			['/','/'.self::Session.'/~','/'.self::User.'/~',"/Admin/1"],
		'MenuExcl' =>
			["/".self::ABB,"/Admin"],			
		self::Application=> [		
				'attrList' => [
					CstView::V_S_REF		=> ['CodeNm'],
				],
				'listHtml' => [
						CstMode::V_S_SLCT => [
								CstView::V_ALIST         => [
										CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,
										CstHTML::H_TABLEN=>9
								],
								CstView::V_ATTR          => CstHTML::H_T_LIST_BR,
						]
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
				'listHtml' => [
						CstMode::V_S_SLCT => [
								CstView::V_ALIST         => [
										CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,
										CstHTML::H_TABLEN=>9
								],
								CstView::V_ATTR          => CstHTML::H_T_LIST_BR,
						]
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

	]
	];		
	
	
	protected static function loadMeta()
	{
		$obj = new Model(Config::ABB);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Name',			Mtype::M_STRING);
		$res = $obj->addAttr('CodeNm',			Mtype::M_STRING);
		$res = $obj->addAttr('Alias',			Mtype::M_STRING);
		$res = $obj->addAttr('ShortDesc',		Mtype::M_STRING);
		$res = $obj->addAttr('LongDesc',		Mtype::M_TXT);
		$res = $obj->addAttr('Owner',			Mtype::M_REF,	"/".Config::Group);
		$res = $obj->addAttr('In',				Mtype::M_CREF,	"/".Config::Interfaces."/Of");
		$res = $obj->addAttr('Out',				Mtype::M_CREF,	"/".Config::Exchange."/OutOf");
		$res = $obj->setAbstr();
		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
		
		// Application
		
		$obj = new Model(Config::Application);
		$res= $obj->deleteMod();
		
		$res = $obj->setInhNme(Config::ABB);
		$res = $obj->addAttr('Style',			Mtype::M_REF,	"/".Config::AStyle);
		$res = $obj->addAttr('Authenticity',	Mtype::M_REF,	"/".Config::SLevel);
		$res = $obj->addAttr('Availability',	Mtype::M_REF,	"/".Config::SLevel);
		$res = $obj->addAttr('Confidentiality',	Mtype::M_REF,	"/".Config::SLevel);
		$res = $obj->addAttr('Integrity',		Mtype::M_REF,	"/".Config::SLevel);
		$res = $obj->addAttr('BuiltFrom',		Mtype::M_CREF,	"/".Config::Component."/Of");
		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
		
		// Component
		
		$obj = new Model(Config::Component);
		$res= $obj->deleteMod();
		
		$res = $obj->setInhNme(Config::ABB);
		$res = $obj->addAttr(Config::CType,		Mtype::M_REF,	"/".Config::CType);
		$res = $obj->addAttr('Of',				Mtype::M_REF,	"/".Config::Application);
		$res = $obj->addAttr('SourceControl',	Mtype::M_REF,	"/".Config::SControl);
		$res = $obj->addAttr('Url',				Mtype::M_STRING);
		$res = $obj->addAttr('Queue',			Mtype::M_STRING);
		$res = $obj->addAttr('OutQueue',		Mtype::M_STRING);
		$res = $obj->addAttr('BatchNme',		Mtype::M_STRING);
		$res = $obj->addAttr('Frequency',		Mtype::M_STRING);
		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
		
		// $Interface
		
		$obj = new Model(Config::Interfaces);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Name',			Mtype::M_STRING);
		$res = $obj->addAttr('Of',				Mtype::M_REF,	"/".Config::ABB);
		$res = $obj->addAttr(Config::IType,		Mtype::M_REF,	"/".Config::IType);
		$res = $obj->addAttr(Config::IUse,		Mtype::M_REF,	"/".Config::IUse);
		$res = $obj->addAttr('Streaming',		Mtype::M_STRING);
		$res = $obj->addAttr('LongDesc',		Mtype::M_TXT);
		$res = $obj->addAttr('Content',			Mtype::M_STRING);
		$res = $obj->addAttr('UsedBy',			Mtype::M_CREF,"/".Config::Exchange."/Through");
		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
		
		// Exchange
		
		$obj = new Model(Config::Exchange);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('CodeNm',			Mtype::M_STRING);
		$res = $obj->addAttr('Through',			Mtype::M_REF,"/".Config::Interfaces);
		$res = $obj->addAttr('OutOf',			Mtype::M_REF,"/".Config::ABB);
		$obj->setCkey(['OutOf','Through'],true);
		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	}
	
	
	
	protected static function loadRole()
	{
		
		// Roles
		
		$RSpec =
		'[
[["Select"],        ["|Application","|Component","|Interface","|Exchange"], "true"],
[["Read"],          "true",         "true"],
[["Read","Update","Delete"],  "|Session",    {"Session":":id"}],
[["Read","Update"],  "|User",       {"User":":id<==>:User"}]
]';
		
		$obj=new Model(Config::Role);
		$obj->setVal('Name','Public');
		$obj->setVal('JSpec',$RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
		
		$RSpec =
		'[
 [["Select"],                      ["|Application","|Component","|Interface","|Exchange"], "true"],
 [["Read"],                        "true",                              "true"],
 [["Update"],                      "|Application",                      {"Application":":Owner<==>:ActiveGroup"}],
 [["Create"],                      "|Application|BuiltFrom",            {"Application":":Owner<==>:ActiveGroup","BuiltFrom":":Owner<>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|In",                   {"Application":":Owner<==>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|Out",                  {"Application":":Owner<==>:ActiveGroup"}],
 [["Update","Delete"],             "|Application|BuiltFrom",            {"BuiltFrom":":Owner<==>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|BuiltFrom|In",         {"BuiltFrom":":Owner<==>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|BuiltFrom|Out",        {"BuiltFrom":":Owner<==>:ActiveGroup"}],
 [["Read","Update","Delete"],      "|Session",                          {"Session":":id"}],
 [["Read","Update"],               "|User",                             {"User":":id<==>:User"}]
]';
		
		
		$obj=new Model(Config::Role);
		$obj->setVal('Name','AppOwner');
		$obj->setVal('JSpec',$RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
		
		$RSpec =
		'[
 [["Select"],                      ["|Application","|Component","|Interface","|Exchange"], "true"],
 [["Read"],                        "true",                              "true"],
 [["Create","Update","Delete"],    "|Application",                      "true"],
 [["Create","Update","Delete"],    "|Component",                        "true"],
 [["Create","Update","Delete"],    "|Interface",                        "true"],
 [["Create","Update","Delete"],    "|Exchange",                         "true"],
 [["Read","Update","Delete"],      "|Session",                          {"Session":":id"}],
 [["Read","Update"],               "|User",                             {"User":":id<==>:User"}]
]';
		
		
		$obj=new Model(Config::Role);
		$obj->setVal('Name','ArchOwner');
		$obj->setVal('JSpec',$RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
		
	}
}
	