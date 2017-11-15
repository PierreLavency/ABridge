<?php

use ABridge\ABridge\AppComp;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;

class Config extends AppComp
{
	const DBDEC = 'ADM';
	
	protected $config= [
			'Hdl' 	=> [					
			],
			'View' => [
					'Home' => [
							'/',
							"/".Adm::ADMIN."/1",
					],
					'MenuExcl' =>[
							"/".Adm::ADMIN,							
					],
			],
			'Apps' => [
					'AdmApp'=>[],
			],			
	];

}
	