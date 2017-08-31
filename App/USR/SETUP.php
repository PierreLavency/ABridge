<?php

class Config 
{
		
	const DBDEC = 'USR';
	
	const User ='User';
	const Session ='Session';
	const Adm ='Admin';
	
	static $config = [
	'Apps'	=>['Usr','Adm'],

	'View' => [
		'Home' =>
			['/',"/".self::Session."/~","/".self::Adm."/1","/".self::User."/~"],
		'MenuExcl' =>
			["/".self::Adm,"/".self::User],
		],
	];
	
	
}
	