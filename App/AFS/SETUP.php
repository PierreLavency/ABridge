<?php
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

class Config
{

	static $config = [
	'Handlers' =>
		[
		'Afs'  => ['dataBase'],
		'Dir'  => ['dataBase'],
		'Fle'  => ['dataBase'],
		],
	'View'=> [
		'Home' =>
			['/Dir','/Fle'],
		]
	];
	
}