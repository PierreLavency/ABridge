<?php
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

class Config
{
	const DBDEC = 'ADM';	
	const Admin ='Admin';
	
	static $config = [
	'Handlers' => [
		self::Admin => ['dataBase',self::DBDEC],
	],

			
	'Adm' => [
			
	],
			
	'View' => [
		'Home' => [
				'/',"/".self::Admin."/1",
		],
		'MenuExcl' =>["/Admin"],
			
		self::Admin =>[		
			'attrList' => [
				CstView::V_S_REF	=> ['id'],
				CstMode::V_S_READ	=> ['id','Application' ,'Init','Load','Meta','Delta','vnum','ctstp','utstp'],
			],
			'lblList'  => [
				CstMode::V_S_UPDT => 'Load',
			],
			'navList' => [
				CstMode::V_S_READ => [CstMode::V_S_UPDT],
			],
		],		

		],
	];		
	
}
	