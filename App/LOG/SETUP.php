<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\App;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\Mod\Model;

require_once 'LogFile.php';
require_once 'LogLine.php';

class Config extends App
{
	const LOGFILE = 'LogFile';
	const LOGLINE = 'LogLine';
	
	protected static $logicalNames =
	[
			self::LOGFILE,
			self::LOGLINE,
	];
	
	public static function  init($prm, $config)
	{
		return $config;
	}
	
	static $config = [
	'Default' =>
			['base'=>'fileBase'],
	'Apps'	=>
			[
					'AdmApp'=>[],		
					
			],
	'Handlers' =>
			[
					self::LOGFILE=> [],
					self::LOGLINE=>[],
			
			],
			
	'View' => [
		'Home' =>
			['/',"/".Adm::ADMIN."/1"],
		'MenuExcl' =>
			[
					"/".Adm::ADMIN,
					
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
		]

		],
	];
	
	public static function initMeta($config)
	{
		AdmApp::initMeta(self::$config['Apps']['AdmApp']);	
		
		$bindings = ModUtils::normBindings(self::$logicalNames);
		
		ModUtils::initModBindings($bindings,self::$logicalNames);		
		
	}
	
	public static function initData($prm=null)
	{
		AdmApp::initData();
		
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
	