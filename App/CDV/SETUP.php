<?php

use ABridge\ABridge\Apps\Cdv;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\App;

class Config extends App
{
			
	static $config = [
	'Apps'	=>
			[
					'AdmApp',
					'Cdv'
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
	
	public static function loadMeta($prm=null)
	{
		AdmApp::loadMeta();
		Cdv::loadMeta(['Sexe','Country']);
		
	}
	
	public static function loadData($prm=null)
	{
		AdmApp::loadData();
		Cdv::loadData(
				[
						'Sexe'=>['Male','Female'],
						'Country'=>['Belgium','France','Italy'],						
				]);
		
		
	}
	
}
	