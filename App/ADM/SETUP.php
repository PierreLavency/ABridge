<?php
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

require_once 'View/CstView.php';

class Config
{
	const DBDEC = 'ADM';
	
	const Adm ='Admin';
	
	static $config = [
	'Handlers' => [
		self::Adm => ['dataBase',self::DBDEC,false],
	],
	'Home' => [
		'/','/Admin/1',
	],
	'Adm' => [
			
	],
	'Views' => [

		self::Adm =>[		
			'attrList' => [
				CstView::V_S_REF		=> ['id'],
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
	