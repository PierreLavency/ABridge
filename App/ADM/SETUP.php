<?php
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

class Config
{
	const DBDEC = 'ADM';	
	const Admin ='Admin';
	
	static $config = [
			
	'Adm' => [
			
	],
	'Hdl' 	=> [

	],
	'View' => [
		'Home' => [
				'/',"/".self::Admin."/1",
		],
		'MenuExcl' =>["/Admin"],
		
		],
						
	'Apps' => [
		'Adm',	
	]
	];		
	
}
	