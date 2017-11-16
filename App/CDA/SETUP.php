<?php

use ABridge\ABridge\Apps\Cda;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\AppCOmp;

class Config extends AppComp
{
	
	protected $config = [
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
	
	
}
	