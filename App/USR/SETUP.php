<?php


use ABridge\ABridge\View\CstView;


class Config 
{
		
	const DBDEC = 'USR';
	
	const User ='User';
	const Role = 'Role';
	const Session ='Session';
	const Distribution = 'Distribution';
	const Group = 'UserGroup';
	const Adm ='Admin';
	
	static $config = [
	'Apps'	=>['Usr','Adm'],
	'Handlers' => [
			self::Group =>['dataBase',]
			],
	'View' => [
		'Home' =>
			['/',"/".self::Session."/~","/".self::Adm."/1","/".self::User."/~"],
		'MenuExcl' =>
			["/".self::Adm,"/".self::User],
		self::Group =>[
					'attrList' => [
							CstView::V_S_REF		=> ['Name'],
					],
			],
		],
	];
	
	
}
	