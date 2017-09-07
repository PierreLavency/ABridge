<?php
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\App;

class Config extends App
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
	
	public  static function loadMeta($prm=null)
	{
		echo 'LoadMeta ';
	}
	
	public  static function loadData($prm=null)
	{
		echo 'LoadData ';;
	}
}
	