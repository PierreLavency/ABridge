<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\App;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Mod\ModUtils;
use ABridge\ABridge\View\CstView;

require_once 'LogMgr.php';
require_once 'LogLine.php';

class Config extends App
{
	const LOGMGR = 'LogMgr';
	const LOGLINE = 'LogLine';
	
	protected static $logicalNames =
	[
			self::LOGMGR,
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
					self::LOGMGR=> [],
					self::LOGLINE=>[],
			
			],
			
	'View' => [
		'Home' =>
			['/',"/".Adm::ADMIN."/1"],
		'MenuExcl' =>
			[
					"/".Adm::ADMIN,
					
			],
		self::LOGMGR=>[
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
		
	}
	
}
	