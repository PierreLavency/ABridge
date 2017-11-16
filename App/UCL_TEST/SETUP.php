<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\AppComp;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Apps\Cdv;
use ABridge\ABridge\Apps\UsrApp;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

require_once 'Student.php';
require_once 'Cours.php';
require_once 'Inscription.php';
require_once 'Prof.php';
require_once 'Charge.php';

class Config extends AppComp
{
		
	const STUDENT = 'Student';
	const COUNTRY = 'Country';
	const SEXE = 'Sexe';
	const COURS = 'Cours';
	const INSCRIPTION ='Inscription';
	const PROF = 'Prof';
	const CHARGE = 'Charge';
	protected  static $logicalNames =
	[
			self::STUDENT,
			self::COURS,
			self::INSCRIPTION,
			self::PROF,
			self::CHARGE,
	];
	

	
	protected $config = [
	'Apps'	=>
			[
					'UsrApp'=>[],
					'AdmApp'=>[],
					'Cdv'=>[
							Cdv::CODELIST=>['Sexe','Country'],
							cdv::CODEDATA=>[
									'Sexe'=>['Male','Female'],
									'Country'=>['Belgium','France','Italy'],
							],
					],
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
//						"/".self::INSCRIPTION,
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
									CstMode::V_S_SLCT => [
											CstMode::V_S_SLCT=>[
													CstView::V_SLICE=>15,CstView::V_COUNTF=>true,CstView::V_CTYP=>CstView::V_C_TYPN]]
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
	
	public function initOwnMeta($config)
	{

		$list = ModUtils::normBindings(self::$logicalNames);
		
		$bindings = array_merge($config,$list);

		
		$obj = new Model($bindings[Usr::USER]);
		$res = $obj->addAttr('Student',Mtype::M_CREF,'/'.self::STUDENT.'/'.$bindings[Usr::USER]);
		$res = $obj->addAttr('Prof',Mtype::M_CREF,'/'.self::PROF.'/'.$bindings[Usr::USER]);
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";		
			

	}
	
	public function initOwnData($prm)
	{

		$RSpec =
'[
 [["Read"],                    "true",                        "true"],
 [["Select"],                 ["|Student","|Cours","|Prof"],  "true"],
 [["Update","Delete"],         "|Student",                   {"Student":":User"}],
 [["Create","Update","Delete"],"|Student|InscritA",          {"Student":":User"}],
 [["Create","Update","Delete"],"|User|Student",              {"User":":id<==>:User"}],
 [["Create","Update","Delete"],"|User|Student|InscritA",     {"User":":id<==>:User"}],
 [ "Select",                   "|User|Student",               "false"],
 [["Read","Update","Delete"],  "|Session",                   {"Session":":id"}],
 [["Read","Update"],           "|User",                      {"User":":id<==>:User"}]
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
 [["Update","Delete"],         "|Prof",                       {"Prof":":User"}],
 [["Create","Update","Delete"],"|User|Prof",                  {"User":":id<==>:User"}],
 [["Read","Update","Delete"],  "|Session",                    {"Session":":id"}],
 [["Read","Update"],           "|User",                       {"User":":id<==>:User"}]
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
 [ "Create",                   "|Cours",            {"Cours":":User"}],
 [ "Create",                   "|Cours",            {"Cours":":UserGroup<>:ActiveGroup"}],
 [["Update","Delete"],         "|Cours",            {"Cours":":UserGroup<>:ActiveGroup"}],
 [["Create","Update","Delete"],"|Cours|Par",        {"Cours":":UserGroup<==>:ActiveGroup"}],
 [["Create","Update","Delete"],"|Cours|SuiviPar",   {"Cours":":UserGroup<==>:ActiveGroup"}],
 [["Update","Read","Delete"],  "|Session",          {"Session":":id"}],
 [["Update","Read"],           "|User",             {"User":":id<==>:User"}]
]';
		
		
		$obj=new Model(Usr::ROLE);
		$obj->setVal('Name','Gestionaire');
		$obj->setVal('JSpec',$RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	}
	
}
	