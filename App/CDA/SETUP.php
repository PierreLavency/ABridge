<?php

use ABridge\ABridge\Apps\Cda;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\App;

class Config extends App
{
	public static function  init($prm, $config)
	{
		return $config;
	}
	
	static $config = [
	'Apps'	=>
			[
					'AdmApp'=>[],		
					'Cda'=>[
							Cda::CODELIST=>['Sexe','Country'],
							cda::CODEDATA=>[
									'Sexe'=>['Male','Female'],
									'Country'=>['Belgium','France','Italy'],
							],
					],
					
			],
	'Handlers' =>
			[
			
			],
			
	'View' => [
		'Home' =>
			['/',"/".Adm::ADMIN."/1"],
		'MenuExcl' =>
			[
					"/".Adm::ADMIN,
					
			],
			

		],
	];
	
	public static function initMeta($config)
	{
		AdmApp::initMeta(self::$config['Apps']['AdmApp']);		
		Cda::initMeta(self::$config['Apps']['Cda']);
		
	}
	
	public static function initData($prm=null)
	{
		AdmApp::initData();
		Cda::initData(self::$config['Apps']['Cda']);
		
		
	}
	
}
	