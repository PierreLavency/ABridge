<?php
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\App;
use ABridge\ABridge\Apps\AdmApp;

class Config extends App
{
	const DBDEC = 'ADM';	
	const Admin ='Admin';
	
	static $config = [

	'Hdl' 	=> [

	],
	'View' => [
		'Home' => [
				'/',"/".self::Admin."/1",
		],
		'MenuExcl' =>["/Admin"],
		
		],
						
	'Apps' => [
		'AdmApp',	
	]
	];
	
	public  static function loadMeta($prm=null)
	{
		AdmApp::loadMeta();
	}
	
	public  static function loadData($prm=null)
	{
		echo 'LoadData ';
	}
}
	