<?php


use ABridge\ABridge\Apps\Cdv;
use ABridge\ABridge\Apps\Adm;

class Config 
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
	
	public static function loadMeta()
	{
		Adm::loadMeta();
		Cdv::loadMeta();
		
	}
	
	public static function loadData()
	{
		Adm::loadData();
		Cdv::loadData(
				[
						'Sexe'=>['Male','Female'],
						'Country'=>['Belgium','France','Italy'],						
				]);
		
		
	}
	
}
	