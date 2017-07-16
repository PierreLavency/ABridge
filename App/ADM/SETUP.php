<?php
require_once 'CstMode.php';
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
				V_S_REF		=> ['id'],
			],
			'lblList'  => [
				V_S_UPDT => 'Load',
			],
			'navList' => [
				V_S_READ => [V_S_UPDT],
			],
		],		

		],
	];		
	
}
	