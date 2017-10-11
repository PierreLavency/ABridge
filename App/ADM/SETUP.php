<?php

use ABridge\ABridge\App;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;

class Config extends App
{
	const DBDEC = 'ADM';
	
	public static function  init($prm, $config)
	{
		return $config;
	}
	
	static $config = [

	'Hdl' 	=> [

	],
	'View' => [
		'Home' => [
				'/',"/".Adm::ADMIN."/1",
		],
			'MenuExcl' =>["/".Adm::ADMIN],
		
		],
						
	'Apps' => [
			'AdmApp'=>[],	
	]
	];

	public static function initMeta($prm,$config)
	{
		AdmApp::initMeta(self::$config['Apps']['AdmApp']);
		echo "ADM initialized\n";
	}
	

	public  static function initData($prm=null)
	{
		echo 'LoadData ';
	}
}
	