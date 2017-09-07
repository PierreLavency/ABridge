<?php

use ABridge\ABridge\Apps\Cdv;
use ABridge\ABridge\Apps\Adm;
use ABridge\ABridge\App;

class Config extends App
{
			
	static $config = [
	'Apps'	=>
			[
					'Adm',
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
		Adm::loadMeta();
		Cdv::loadMeta(['Sexe','Country']);
		
	}
	
	public static function loadData($prm=null)
	{
		Adm::loadData();
		Cdv::loadData(
				[
						'Sexe'=>['Male','Female'],
						'Country'=>['Belgium','France','Italy'],						
				]);
		
		
	}
	
}
	