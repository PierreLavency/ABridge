<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\View\CstHTML;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Apps\Usr;
use ABridge\ABridge\Apps\Adm;
use ABridge\ABridge\Apps\Cdv;

use ABridge\ABridge\UtilsC;
use ABridge\ABridge\App;

require_once 'Student.php';
require_once 'Cours.php';
require_once 'Inscription.php';
require_once 'Prof.php';
require_once 'Charge.php';

class Config extends App
{
		
	const STUDENT = 'Student';
	const COUNTRY = 'Country';
	const SEXE = 'Sexe';
	const COURS = 'Cours';
	const INSCRIPTION ='Inscription';
	const PROF = 'Prof';
	const CHARGE = 'Charge';
	
	static $config = [
	'Apps'	=>
			[
					'Usr',
					'Adm',
					'Cdv'
			],
	'Handlers' =>
			[
					self::STUDENT =>[],
					self::COURS =>[],
					self::INSCRIPTION=>[],
					self::PROF=>[],
					self::CHARGE=>[],
			],
			
	'View' => 
			[
			'Home' =>
				['/',"/".Usr::SESSION."/~","/".Usr::USER."/~","/".Adm::ADMIN."/1"],
			'MenuExcl' =>
				[
						"/".Adm::ADMIN,"/".Usr::USER,"/".Usr::SESSION,
						"/".Usr::DISTRIBUTION,"/".Usr::GROUPUSER,
						"/".Cdv::CODEVAL,
						"/".self::INSCRIPTION,
						"/".self::CHARGE,
				],
			'modLblList'=>[Usr::USERGROUP=>'Group'],
			Usr::USER => 
					[
						'viewList' => [
								'Profile' => [
										'attrList' => [
												CstMode::V_S_READ=> [
														'id',
														'UserId',
														'Role',
														'UserGroup',
														'Student',
														'Prof',
												],
										],
										'navList' => [
												CstMode::V_S_READ => [
														CstMode::V_S_SLCT,CstMode::V_S_CREA,CstMode::V_S_DELT,
												],
										],
								],
						],
							
					],
			self::STUDENT => 
					[						
						'attrList' => 
							[
									CstView::V_S_REF		=> ['SurName','Name'],
							],
						'attrHtml' => 
							[
									CstMode::V_S_CREA => ['Sexe'=>CstHTML::H_T_RADIO],
									CstMode::V_S_UPDT => ['Sexe'=>CstHTML::H_T_RADIO],
									CstMode::V_S_SLCT => ['Sexe'=>CstHTML::H_T_RADIO],
									CstMode::V_S_READ => ['InscritA'=>[CstView::V_SLICE=>5,CstView::V_COUNTF=>true,CstView::V_CTYP=>CstView::V_C_TYPN]]
							],
						'attrProp' => 
							[
									CstMode::V_S_SLCT =>[CstView::V_P_LBL,CstView::V_P_OP,CstView::V_P_VAL],
							],
						'lblList' => 
							[
									'id'		=> 'Noma',
									'Name' 		=> 'Nom',
									'SurName' 	=> 'Prenom',
									'BirthDay'	=> 'Date de naissance',
							],
						'viewList' => 
							[
								'Resume'  => 
									[
										'attrList' => 
											[
													CstMode::V_S_READ=> ['id','SurName','Name','NbrCours','NbrCredits'],
													CstMode::V_S_UPDT=> ['SurName','Name'],
													CstMode::V_S_CREA=> ['SurName','Name'],
													CstMode::V_S_DELT=> ['SurName','Name'],
											],
									],
								'Detail'  => 
									[
										'attrList' => 
											[
													CstMode::V_S_READ=> ['SurName','Name','BirthDay','Sexe','Country',],
													CstMode::V_S_UPDT=> ['SurName','Name','BirthDay','Sexe','Country'],
											],
										'navList' => 
											[
													CstMode::V_S_READ => [CstMode::V_S_UPDT],
											],
									],
								'Inscription' =>
									[
										'attrList' => 
											[
													CstMode::V_S_READ=> ['SurName','Name','InscritA'],
											],
										'navList' => 
											[
													CstMode::V_S_READ => []
											],
									],
								'Image' =>
									[
										'attrProp' => 
											[
													CstMode::V_S_READ =>[CstView::V_P_VAL],
											],
										'attrList' => 
											[
													CstMode::V_S_READ=> ['Image'],
													CstMode::V_S_UPDT=> ['Image'],
											],
										'attrHtml' => 
											[
													CstMode::V_S_READ => ['Image'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 80]],
											],
										'navList' => 
											[
													CstMode::V_S_READ => [CstMode::V_S_UPDT]
											],
									],
								'Jason' =>
									[
										'attrProp' => 
											[
													CstMode::V_S_READ =>[CstView::V_P_VAL],
											],
										'attrList' => 
											[
													CstMode::V_S_READ=> ['Jason'],
											],
										'attrHtml' => 
											[
													CstMode::V_S_READ => ['Jason'=>[CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_COL=>90,CstHTML::H_ROW=> 40]],
											],
										'navList' => 
											[
													CstMode::V_S_READ => []
											],
									],
								'Trace' =>
									[
										'attrList' => 
											[
													CstMode::V_S_READ=> ['id','vnum','ctstp','utstp','User'],
													CstMode::V_S_UPDT=> ['User'],
											],
										'navList' => 
											[
													CstMode::V_S_READ => [CstMode::V_S_UPDT]
											],
										'attrHtml' => 
											[
													CstMode::V_S_UPDT => ['User'=>CstHTML::H_T_SELECT],
											]
									],
							],					
	
					],
			self::COURS =>
					[							
						'attrList' => 
							[
									CstView::V_S_REF	=> ['SurName','Name'],
									CstView::V_S_CREF	=> ['id','Credits','User'],
							],						
						'attrHtml' => 
							[
									CstMode::V_S_UPDT => ['User'=>CstHTML::H_T_SELECT],
									CstMode::V_S_SLCT => [CstMode::V_S_SLCT=>[CstView::V_SLICE=>15,CstView::V_COUNTF=>true,CstView::V_CTYP=>CstView::V_C_TYPN]]
							]
							
					],
			self::PROF =>
					[
						'attrList' => 
							[
									CstView::V_S_REF	=> ['SurName','Name'],
									CstView::V_S_CREF	=> ['id','User'],
							]
							
					],
			self::INSCRIPTION =>
					[
							
						'attrHtml' => 
							[
									CstMode::V_S_CREA => ['A'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
									CstMode::V_S_READ => ['De'=>'Resume'],
									CstMode::V_S_UPDT => ['A'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
									CstMode::V_S_SLCT => ['A'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
							],
							
					],
			self::CHARGE =>
					[							
						'attrHtml' => 
							[
									CstMode::V_S_CREA => ['Par'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
									CstMode::V_S_UPDT => ['Par'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
									CstMode::V_S_SLCT => ['Par'=>CstHTML::H_T_SELECT,'De'=>CstHTML::H_T_SELECT],
							],
							
					],
			]
	];
	
	public static function loadMeta($prm=null)
	{
		Usr::loadMeta();
		
		$obj = new Model(Usr::USER);
		$res = $obj->addAttr('Student',Mtype::M_CREF,'/'.self::STUDENT.'/'.Usr::USER);
		$res = $obj->addAttr('Prof',Mtype::M_CREF,'/'.self::PROF.'/'.Usr::USER);
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
		
		Adm::loadMeta();
		Cdv::loadMeta([self::SEXE,self::COUNTRY]);

		$bindings=
		[
				self::SEXE=>Cdv::CODE.'/1',
				self::COUNTRY=>Cdv::CODE.'/2',
				Usr::USER=>Usr::USER,
				Usr::USERGROUP=>Usr::USERGROUP,
				self::INSCRIPTION=>self::INSCRIPTION,
				self::CHARGE=>self::CHARGE,
				self::COURS=>self::COURS,
				self::STUDENT=>self::STUDENT,
				self::PROF=>self::PROF,
				
		];

		$logicalNames =
		[
				self::STUDENT,
				self::COURS,
				self::INSCRIPTION,
				self::PROF,
				self::CHARGE,
		];
			
		UtilsC::createMods($bindings,$logicalNames);		

	}
	
	public static function loadData($prm=null)
	{
		Usr::loadData();
		Adm::loadData();
		Cdv::loadData(
				[
						self::SEXE=>['Male','Female'],
						self::COUNTRY=>['Belgium','France','Italy'],
				]);
		self::loadDataRole();
	}

	public  static function loadDataRole()
	{		
		$RSpec =
'[
 [["Read"],                    "true",                        "true"],
 [["Select"],                 ["|Student","|Cours","|Prof"],  "true"],
 [["Update","Delete"],         "|Student",                   {"Student":"User"}],
 [["Create","Update","Delete"],"|Student|InscritA",          {"Student":"User"}],
 [["Create","Update","Delete"],"|User|Student",              {"User":"id<>User"}],
 [["Create","Update","Delete"],"|User|Student|InscritA",     {"User":"id<>User"}],
 [ "Select",                   "|User|Student",               "false"],
 [["Read","Update","Delete"],  "|Session",                   {"Session":"id"}],
 [["Read","Update"],           "|User",                      {"User":"id<>User"}]
]';
		
		$obj=new Model(Usr::ROLE);
		$obj->setVal('Name','Student');
		$obj->setVal('JSpec',$RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
		
		$RSpec =
'[
 [["Read"],                    "true",                        "true"],
 [["Select"],                 ["|Student","|Cours","|Prof"],  "true"],
 [["Update","Delete"],         "|Prof",                       {"Prof":"User"}],
 [["Create","Update","Delete"],"|User|Prof",                  {"User":"id<>User"}],
 [["Read","Update","Delete"],  "|Session",                    {"Session":"id"}],
 [["Read","Update"],           "|User",                       {"User":"id<>User"}]
]';
		
		
		$obj=new Model(Usr::ROLE);
		$obj->setVal('Name','Professor');
		$obj->setVal('JSpec',$RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
		
		$RSpec =
'[
 [["Read"],                    "true",              "true"],
 [["Select"],  ["|Student","|Cours","|Prof","|Inscription","|Charge"],        "true"],
 [ "Create",                   "|Cours",            {"Cours":"User"}],
 [ "Create",                   "|Cours",            {"Cours":"UserGroup<>ActiveGroup"}],
 [["Update","Delete"],         "|Cours",            {"Cours":"UserGroup<>ActiveGroup"}],
 [["Create","Update","Delete"],"|Cours|Par",        {"Cours":"UserGroup<>ActiveGroup"}],
 [["Create","Update","Delete"],"|Cours|SuiviPar",   {"Cours":"UserGroup<>ActiveGroup"}],
 [["Update","Read","Delete"],  "|Session",          {"Session":"id"}],
 [["Update","Read"],           "|User",             {"User":"id<>User"}]
]';
		
		
		$obj=new Model(Usr::ROLE);
		$obj->setVal('Name','Gestionaire');
		$obj->setVal('JSpec',$RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	}
	
}
	