<?php

use ABridge\ABridge\Apps\Cdv;
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
		
					'Cdv'=>[
							Cdv::CODE=>'PersonCodes',
							Cdv::CODEVAL=>'PersonCodeValues',
							Cdv::CODELIST=>['Sexe','Country'],
							cdv::CODEDATA=>[
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
					"/".Cdv::CODEVAL,

					
			],
			

		],
	];
	
	public static function initMeta($config)
	{
		AdmApp::initMeta($prm,self::$config['Apps']['AdmApp']);		
		Cdv::initMeta($prm,self::$config['Apps']['Cdv']);
		
	}
	
	public static function initData($prm=null)
	{
		AdmApp::initData();
		Cdv::initData(self::$config['Apps']['Cdv']);
		
		
	}
	
}
	