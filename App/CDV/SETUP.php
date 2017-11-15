<?php

use ABridge\ABridge\Apps\Cdv;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\AppComp;

class Config extends AppComp
{
	
	protected $config = [
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
	
	
}
	