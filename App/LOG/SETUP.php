<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\AppComp;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Apps\Cdv;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\View\CstHTML;

require_once 'LogFile.php';
require_once 'LogLine.php';
require_once 'PrfFile.php';
require_once 'PrfLine.php';

class Config extends AppComp
{
	const LOGFILE = 'LogFile';
	const LOGLINE = 'LogLine';
	const PRFFILE = 'PrfFile';
	const PRFLINE = 'PrfLine';
	
	protected static $logicalNames =
	[
			self::LOGFILE,
			self::LOGLINE,
			self::PRFFILE,
			self::PRFLINE,
	];
	
	
	protected $config = [
	'Default' =>
			['base'=>'fileBase'],
	'Apps'	=>
			[
					'AdmApp'=>[],
					'Cdv'=>[
							Cdv::CODE=>'Codes',
							Cdv::CODEVAL=>'CodeValues',
							Cdv::CODELIST=>['Operation','Base','Access'],
							cdv::CODEDATA=>[
									'Operation'=>[CstMode::V_S_READ,CstMode::V_S_UPDT,CstMode::V_S_CREA,CstMode::V_S_DELT,],
									'Base'=>['dataBase','fileBase','memBase',],
									'Access'=>['NA','Root','Usr',],
							],
					],
					
			],
	'Handlers' =>
			[
					self::LOGFILE=> [],
					self::LOGLINE=>[],
					self::PRFFILE=> [],
					self::PRFLINE=>[],
			],
			
	'View' => [
		'Home' =>
			['/',"/".Adm::ADMIN."/1"],
		'MenuExcl' =>
			[
					"/".Adm::ADMIN,"/".Self::LOGLINE,
					
			],
		self::LOGFILE=>[
				'attrList' => [
							CstView::V_S_REF        => ['Name'],
					],
				'attrHtml' => [
						CstMode::V_S_READ => [
								'Lines'=>[
										CstView::V_SLICE=>1,
										CstView::V_COUNTF=>true,
										CstView::V_CTYP=>CstView::V_C_TYPN,
										CstView::V_B_NEW=>false,
								]
						],
				],
				'viewList'=> [
						'Summary' =>
						[
								'attrList' =>
								[
										CstMode::V_S_READ=>
										[
												'id',
												'Name',
												'LoadedLines',
												'Load',
												'Path',
										],
										CstMode::V_S_UPDT=>
										[
												'Name',
												'Load',
										],
								],
								'navList' =>
								[
										CstMode::V_S_READ =>
										[
												CstMode::V_S_UPDT,
												CstMode::V_S_DELT
												
										],
								],
						],
						'Lines' => [
								'attrList' =>
								[
										CstMode::V_S_READ=>
										[
												'Name',
												'Lines',
										],

								],
								'navList' =>
								[
										CstMode::V_S_READ =>
										[
												
										],
								],
						]
				]
			],
		self::LOGLINE => [
				'attrList' => [
						CstView::V_S_CREF	=> ['id','Content'],
				],
		],
	
		self::PRFLINE => [
				'listHtml' => [
						CstMode::V_S_SLCT => [
								CstView::V_ALIST         => [
										CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,
										CstHTML::H_TABLEN=>9
								],
								CstView::V_ATTR          => [
										CstHTML::H_TYPE=>CstHTML::H_T_LIST_BR,
										],
						]
				],
				'attrHtml' => [
						CstMode::V_S_SLCT => [
								CstMode::V_S_SLCT=>[
										CstView::V_SLICE=>17,
										CstView::V_CREFLBL=>true,
								]
						],
				],
		],
		self::PRFFILE => [
					'attrList' => [
							CstView::V_S_REF	=> ['ExecTime'],
							CstView::V_S_CREF => ['id','ExecTime','LoadedLines']
					],

					'attrHtml' => [
							CstMode::V_S_READ => [
									'Lines'=>[
											CstView::V_B_NEW=>false,
											CstView::V_CREFLBL=>true,
											CstView::V_B_SLC=>true,
									]
							],
							CstMode::V_S_SLCT => [
									CstMode::V_S_SLCT=>[
											CstView::V_SLICE=>15,
											
									]
							],
					],
			],

		],
	];
	
	public function initOwnMeta($bindings)
	{
	
		$list = ModUtils::normBindings(self::$logicalNames);
		
		$bindings = array_merge($bindings,$list);

		ModUtils::initModBindings($bindings,self::$logicalNames);
		
		return $bindings;
		
	}
	
	public function initOwnData($prm)
	{		
		$logs = [
				'View_init',
				'View_init_testRun',
				'View_init_Xref',
				'View_init_Xref_testRun',
				'GenHTML_init',
				'GenHTML_init_testRun',
				'GenJason_init',
				'GenJason_init_testRun',
		];
		
		foreach ($logs as $log) {
			$x= new Model(self::LOGFILE);
			$x->setVal('Name',$log);
			$x->setVal('Load', 'true');
			$x->save();
		}
		
	}
	
}
	